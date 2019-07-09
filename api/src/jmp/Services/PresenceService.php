<?php

namespace jmp\Services;

use jmp\Models\Presence;
use jmp\Utils\Optional;
use PDO;
use Psr\Container\ContainerInterface;

class PresenceService
{
    /**
     * @var PDO
     */
    protected $db;

    /**
     * EventService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
    }

    /**
     * @param Presence $presence
     * @return bool
     */
    public function deletePresence(Presence $presence): bool
    {
        $sql = <<< SQL
DELETE FROM presence
WHERE event_id = :eventId
  AND user_id = :userId
  AND auditor_id = :auditorId
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':eventId', $presence->event);
        $stmt->bindParam('userId', $presence->user);
        $stmt->bindParam('auditorId', $presence->auditor);

        return $stmt->execute();
    }

    /**
     * @param Presence $presence
     * @return Optional
     */
    public function updatePresence(Presence $presence): Optional
    {
        $sql = <<< SQL
UPDATE presence
SET has_attended = :hasAttended
WHERE event_id = :eventId
  AND user_id = :userId
  AND auditor_id = :auditorId
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':hasAttended', $presence->hasAttended, PDO::PARAM_BOOL);
        $stmt->bindParam(':eventId', $presence->event);
        $stmt->bindParam('userId', $presence->user);
        $stmt->bindParam('auditorId', $presence->auditor);

        if ($stmt->execute() === false) {
            return Optional::failure();
        }

        return $this->getPresenceByIds($presence->event, $presence->user, $presence->auditor);
    }

    /**
     * @param Presence $presence
     * @return Optional
     */
    public function createPresence(Presence $presence): Optional
    {
        $sql = <<<SQL
INSERT INTO presence (event_id, user_id, auditor_id, has_attended)
VALUES (:eventId, :userId, :auditorId, :hasAttended)
ON DUPLICATE KEY UPDATE event_id=event_id, user_id=user_id, auditor_id=auditor_id
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':eventId', $presence->event);
        $stmt->bindParam(':userId', $presence->user);
        $stmt->bindParam(':auditorId', $presence->auditor);
        $stmt->bindParam(':hasAttended', $presence->hasAttended);

        if ($stmt->execute() === false) {
            return Optional::failure();
        }

        return $this->getPresenceByIds($presence->event, $presence->user, $presence->auditor);
    }

    /**
     * @param int $eventId
     * @param int $userId
     * @param int $auditorId
     * @return Optional
     */
    public function getPresenceByIds(int $eventId, int $userId, int $auditorId): Optional
    {
        $sql = <<< SQL
SELECT event_id as event,
       user_id as user,
       auditor_id as auditor,
       has_attended as hasAttended
       FROM presence
WHERE event_id = :eventId
 AND user_id = :userId
 AND auditor_id = :auditorId
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':auditorId', $auditorId);

        $stmt->execute();

        $presence = $stmt->fetch();
        if ($presence === false) {
            return Optional::failure();
        }

        return Optional::success(new Presence($presence));
    }
}
