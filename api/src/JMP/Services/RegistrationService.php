<?php

namespace JMP\Services;

use JMP\Models\Registration;
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
     * RegistrationService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
        $this->registrationStateService = $container->get('registrationStateService');
    }

    /**
     * @param int $userId
     * @param int $eventId
     * @return Registration
     */
    public function getRegistrationByUserIdAndEventId(int $userId, int $eventId): Registration
    {
        $sql = <<< SQL
SELECT event_id as eventId, user_id as userId, reason, registration_state_id as regStateId
FROM registration
WHERE event_id = :eventId AND user_id = :userId
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $val = $stmt->fetch();
        if (!$val) {
            return null;
        }
        $registration = new Registration($val);
        $registration->registrationState = $this->registrationStateService->getRegistrationTypeById($val['regStateId']);
        return $registration;
    }

    /**
     * Delete all registrations of a user
     * @param int $userId
     * @return Registration|null
     */
    public function deleteRegistrationsOfUser(int $userId): void
    {
        $sql = <<< SQL
            DELETE FROM registration
            WHERE user_id = :userId
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
    }

}
