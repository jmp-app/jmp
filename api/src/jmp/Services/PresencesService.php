<?php

namespace jmp\Services;

use jmp\Models\Presence;
use jmp\Models\User;
use jmp\Utils\Optional;
use PDO;
use PDOStatement;
use Psr\Container\ContainerInterface;

class PresencesService
{
    /**
     * @var PDO
     */
    protected $db;

    /**
     * @var PresenceService
     */
    protected $presenceService;

    /**
     * EventService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
        $this->presenceService = $container->get('presenceService');
    }

    /**
     * @param int $eventId
     * @param array $presences
     * @return bool
     */
    public function deletePresences(array $presences): bool
    {
        $this->db->beginTransaction();
        foreach ($presences as $presence) {
            $success = $this->presenceService->deletePresence($presence);
            if ($success === false) {
                $this->db->rollBack();
                return false;
            }
        }
        return $this->db->commit();
    }

    public function createPresences(int $eventId, array $presences): Optional
    {
        $sql = <<< SQL
INSERT INTO presence
(event_id, user_id, auditor_id, has_attended)
VALUES insertions
ON DUPLICATE KEY UPDATE event_id=event_id, user_id=user_id, auditor_id = auditor_id
SQL;

        list($insertQuery, $insertData) = $this->prepareInsertArray($presences);

        if (!empty($insertQuery)) {
            $sql = str_replace('insertions', implode(', ', $insertQuery), $sql);
            $stmt = $this->db->prepare($sql);
            if ($stmt->execute($insertData) === false) {
                return Optional::failure();
            }
        }

        return $this->getExtendedPresencesByEventId($eventId);
    }

    /**
     * @param int $eventId
     * @param array $presences
     * @return Optional
     */
    public function updatePresences(int $eventId, array $presences): Optional
    {
        $this->db->beginTransaction();
        foreach ($presences as $presence) {
            $optional = $this->presenceService->updatePresence($presence);
            if ($optional->isFailure()) {
                $this->db->rollBack();
                return Optional::failure();
            }
        }
        $this->db->commit();

        return $this->getExtendedPresencesByEventId($eventId);
    }

    /**
     * @param array $presences
     * @return array
     */
    private function prepareInsertArray(array $presences): array
    {
        $insertQuery = [];
        $insertData = [];

        foreach ($presences as $presence) {
            /** @var Presence $presence */
            $insertQuery[] = '(?, ?, ?, ?)';
            $insertData[] = $presence->event;
            $insertData[] = $presence->user;
            $insertData[] = $presence->auditor;
            $insertData[] = $presence->hasAttended ? 1 : 0;
        }
        return array($insertQuery, $insertData);
    }

    /**
     * @param int $eventId
     * @return Optional
     */
    public function getExtendedPresencesByEventId(int $eventId): Optional
    {
        $sql = <<< SQL
SELECT DISTINCT u.id,
                u.username,
                u.lastname,
                u.firstname,
                u.email,
                u.is_admin as isAdmin,
                p.auditor_id as auditor,
                p.has_attended as hasAttended
FROM presence p
       LEFT JOIN event e on p.event_id = e.id
       LEFT JOIN event_has_group ehg on e.id = ehg.event_id
       LEFT JOIN `group` g on ehg.group_id = g.id
       LEFT JOIN membership m on g.id = m.group_id
       LEFT JOIN user u on m.user_id = u.id
WHERE p.event_id = :eventId
  AND u.id = p.user_id
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->execute();

        $data = $stmt->fetchAll();
        return $this->fetchAndTransformPresences($data, $stmt);

    }

    /**
     * @param array $data
     * @param PDOStatement $stmt
     * @return Optional
     */
    private function fetchAndTransformPresences(array $data, PDOStatement $stmt): Optional
    {
        if ($data === false) {
            # Return an empty array if there are no registrations for an event
            return $stmt->rowCount() === 0 ? Optional::success([]) : Optional::failure();
        }

        return $this->fetchPresencesToExtendedPresences($data);
    }

    /**
     * @param array $data
     * @return Optional
     */
    private function fetchPresencesToExtendedPresences(array $data): Optional
    {
        foreach ($data as $key => $presenceData) {
            $data[$key] = $this->transformPresence($presenceData);
        }
        return Optional::success($data);
    }

    /**
     * @param array $presenceData
     * @return User
     */
    private function transformPresence(array $presenceData): User
    {
        $user = new User($presenceData);
        $presence = new Presence($presenceData);
        unset($presence->event);
        unset($presence->user);
        $user->presence = $presence;
        return $user;
    }


}
