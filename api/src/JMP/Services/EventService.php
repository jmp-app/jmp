<?php

namespace JMP\Services;


use JMP\Models\Event;
use PDO;
use Psr\Container\ContainerInterface;

class EventService
{
    /**
     * @var \PDO
     */
    protected $db;
    /**
     * @var EventTypeService
     */
    protected $eventTypeService;
    /**
     * @var GroupService
     */
    protected $groupService;
    /**
     * @var RegistrationStateService
     */
    protected $registrationStateService;

    /**
     * EventService constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
        $this->eventTypeService = $container->get('eventTypeService');
        $this->groupService = $container->get('groupService');
        $this->registrationStateService = $container->get('registrationStateService');
    }

    /**
     * @param int $groupId
     * @param int $eventTypeId
     * @return Event[]
     */
    public function getEventsByGroupAndEventType(?int $groupId, ?int $eventTypeId): array
    {
        $sql = <<< SQL
                SELECT event.id,
                       event.title,
                       event.description,
                       `from`,
                       `to`,
                       place,
                       event_type_id AS eventTypeId,
                       default_registration_state_id AS defaultRegistrationState
                FROM event
                RIGHT JOIN event_has_group ehg ON event.id = ehg.event_id
                RIGHT JOIN event_type ON event.event_type_id = event_type.id
                WHERE (:groupId IS NULL OR ehg.group_id = :groupId)
                  AND (:eventType IS NULL OR event_type_id = :eventType)
                ORDER BY event.`from` DESC 
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->bindValue(':eventType', $eventTypeId, PDO::PARAM_INT);


        return $this->fetchAllEvents($stmt);
    }

    /**
     * @param int $groupId
     * @param int $eventTypeId
     * @param int $limit
     * @param int $offset
     * @return Event[]
     */
    public function getEventsByGroupAndEventTypeWithPagination(?int $groupId, ?int $eventTypeId, int $limit, int $offset = 0): array
    {
        $sql = <<< SQL
                SELECT event.id,
                       event.title,
                       event.description,
                       `from`,
                       `to`,
                       place,
                       event_type_id AS eventTypeId,
                       default_registration_state_id AS defaultRegistrationState
                FROM event
                RIGHT JOIN event_has_group ehg ON event.id = ehg.event_id
                RIGHT JOIN event_type ON event.event_type_id = event_type.id
                WHERE (:groupId IS NULL OR ehg.group_id = :groupId)
                  AND (:eventType IS NULL OR event_type_id = :eventType)
                ORDER BY event.`from` DESC 
                LIMIT :lim OFFSET :off
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->bindValue(':eventType', $eventTypeId, PDO::PARAM_INT);
        $stmt->bindParam(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':off', $offset, PDO::PARAM_INT);

        return $this->fetchAllEvents($stmt);
    }

    /**
     * Get all events with offset.
     * @param int $groupId
     * @param int $eventTypeId
     * @param int $offset
     * @return Event[]
     */
    public function getEventByGroupAndEventWithOffset(int $groupId, int $eventTypeId, int $offset = 0): array
    {
        $events = $this->getEventsByGroupAndEventType($groupId, $eventTypeId);
        return array_slice($events, $offset);
    }

    /**
     * Executes the statement and parses its result to return a list of events.
     * @param \PDOStatement $stmt
     * @return Event[]
     */
    private function fetchAllEvents(\PDOStatement $stmt): array
    {
        $stmt->execute();

        $events = $stmt->fetchAll();

        foreach ($events as $key => $val) {
            $event = new Event($val);
            $event->eventType = $this->eventTypeService->getEventTypeByEvent($val['eventTypeId']);
            $event->defaultRegistrationState = $this->registrationStateService->getRegistrationTypeById($val['defaultRegistrationState']);
            $event->groups = $this->groupService->getGroupsByEventId($val['id']);
            $events[$key] = $event;
        }

        return $events;
    }

}