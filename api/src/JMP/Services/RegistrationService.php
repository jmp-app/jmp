<?php

namespace JMP\Services;

use JMP\Models\Registration;
use JMP\Models\User;
use JMP\Utils\Optional;
use Psr\Container\ContainerInterface;

class RegistrationService
{
    /**
     * @var \PDO
     */
    protected $db;
    /**
     * @var RegistrationStateService
     */
    protected $registrationStateService;
    /**
     * @var EventService
     */
    protected $eventService;
    /**
     * @var User $user
     */
    private $user;

    /**
     * RegistrationService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
        $this->registrationStateService = $container->get('registrationStateService');
        $this->eventService = $container->get('eventService');
        $this->user = $container->get('user');
    }

    /**
     * @param int $userId
     * @param int $eventId
     * @return Optional
     */
    public function getRegistrationByUserIdAndEventId(int $userId, int $eventId): Optional
    {
        $sql = <<< SQL
SELECT DISTINCT registration.event_id as eventId,
                registration.user_id  as userId,
                reason,
                registration_state_id as registrationStateId
FROM registration
       LEFT JOIN event e on registration.event_id = e.id
       LEFT JOIN event_has_group ehg on e.id = ehg.event_id
       LEFT JOIN `group` g on ehg.group_id = g.id
       LEFT JOIN membership m on g.id = m.group_id
       LEFT JOIN user u on m.user_id = u.id
WHERE registration.event_id = :eventId
  AND registration.user_id = :userId
  AND (:isAdmin IS TRUE OR u.username = :username)
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':eventId', $eventId, \PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, \PDO::PARAM_INT);
        $stmt->bindParam(':isAdmin', $this->user->isAdmin);
        $stmt->bindParam(':username', $this->user->username);
        $stmt->execute();

        $data = $stmt->fetch();

        if ($data == false) {
            return Optional::failure();
        }

        return $this->fetchRegistration($data);
    }

    /**
     * Insert new Registration into DB
     * @param Registration $registration
     * @return Optional
     */
    public function createRegistration(Registration $registration): Optional
    {
        $sql = <<< SQL
INSERT INTO registration
  (event_id, user_id, reason, registration_state_id)
  VALUES (:eventId, :userId, :reason, :registrationStateId)
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':eventId', $registration->eventId, \PDO::PARAM_INT);
        $stmt->bindParam(':userId', $registration->userId, \PDO::PARAM_INT);
        $stmt->bindParam(':reason', $registration->reason);
        $stmt->bindParam(':registrationStateId', $registration->registrationState->id, \PDO::PARAM_INT);

        $successful = $stmt->execute();
        if ($successful === false) {
            return Optional::failure();
        }

        return $this->getRegistrationByUserIdAndEventId($registration->userId, $registration->eventId);

    }

    /**
     * Update a registration
     * @param Registration $registration with values to update
     * @return Optional
     */
    public function updateRegistration(Registration $registration): Optional
    {
        $sql = <<< SQL
UPDATE registration
  SET reason = :reason, registration_state_id = :registrationStateId
    WHERE
      event_id = :eventId AND user_id = :userId
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':reason', $registration->reason, \PDO::PARAM_STR);
        $stmt->bindParam(':registrationStateId', $registration->registrationState->id, \PDO::PARAM_INT);
        $stmt->bindParam(':eventId', $registration->eventId, \PDO::PARAM_INT);
        $stmt->bindParam(':userId', $registration->userId, \PDO::PARAM_INT);

        $successful = $stmt->execute();
        if ($successful === false) {
            return Optional::failure();
        }

        return $this->getRegistrationByUserIdAndEventId($registration->userId, $registration->eventId);
    }

    /**
     * Delete all registrations of a user
     * @param int $userId
     * @return bool
     */
    public function deleteRegistrationsOfUser(int $userId): bool
    {
        $sql = <<< SQL
            DELETE FROM registration
            WHERE user_id = :userId
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        return $stmt->execute();
    }

    /**
     * Deletes a registration of a userl
     * @param int $userId
     * @param int $eventId
     * @return bool
     */
    public function deleteRegistration(int $userId, int $eventId): bool
    {
        $sql = <<< SQL
DELETE FROM jmp.registration
WHERE jmp.registration.event_id = :eventId
  AND jmp.registration.user_id = :userId
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->bindParam(':userId', $userId);
        return $stmt->execute();
    }

    /**
     * @param $data
     * @return Optional
     */
    private function fetchRegistration(array $data): Optional
    {
        $registration = new Registration($data);

        $registrationStateId = $data['registrationStateId'];
        $optional = $this->registrationStateService->getRegistrationStateById($registrationStateId);

        if ($optional->isFailure()) {
            return Optional::failure();
        } else {
            $registration->registrationState = $optional->getData();
            return Optional::success($registration);
        }
    }

}
