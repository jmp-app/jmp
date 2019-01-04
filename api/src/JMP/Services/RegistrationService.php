<?php

namespace JMP\Services;

use JMP\Models\Registration;
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
     * RegistrationService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
        $this->registrationStateService = $container->get('registrationStateService');
        $this->eventService = $container->get('eventService');
    }

    /**
     * @param int $userId
     * @param int $eventId
     * @return Optional
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
        if ($val === false) {
            return $this->getRegistrationByDefaultRegistrationStateInEvent($userId, $eventId);
        }
        $registration = new Registration($val);
        $optional = $this->registrationStateService->getRegistrationTypeById($val['regStateId']);
        if ($optional->isSuccess()) {
            $registration->registrationState = $optional->getData();
        }
        return Optional::success($registration);
    }

    /**
     * @param int $userId
     * @param int $eventId
     * @return Optional
     */
    private function getRegistrationByDefaultRegistrationStateInEvent(int $userId, int $eventId)
    {
        $optional = $this->eventService->getEventById($eventId);

        if ($optional->isFailure()) {
            return Optional::failure();
        } else {
            $registration = new Registration([$userId, $eventId, '']);
            $registration->registrationState = $optional->getData()->defaultRegistrationState;
            return Optional::success($registration);
        }
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

        $stmt->bindParam(':eventId', $registration->eventId);
        $stmt->bindParam(':userId', $registration->userId);
        $stmt->bindParam(':reason', $registration->reason);
        $stmt->bindParam(':registrationStateId', $registration->registrationState->id);

        $stmt->execute();

        return $this->getRegistrationByUserIdAndEventId($registration->userId, $registration->eventId);

    }
}