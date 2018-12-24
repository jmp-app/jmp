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
    public function getRegistrationByUserIdAndEventId(int $userId, int $eventId)
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
        $registration = new Registration($val); //TODO (simon): Catch if $val no data is found
        $registration->registrationState = $this->registrationStateService->getRegistrationTypeById($val['regStateId']);
        return $registration;
    }
}