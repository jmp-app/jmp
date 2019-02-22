<?php

namespace JMP\Services;


use JMP\Models\Event;
use JMP\Models\User;
use JMP\Utils\Optional;
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
     * @param ContainerInterface $container
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
     * @param User $user
     * @return Event[]
     */
    public function getEventsByGroupAndEventType(?int $groupId, ?int $eventTypeId, User $user): array
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
                RIGHT JOIN `group` g on ehg.group_id = g.id
                RIGHT JOIN membership m on g.id = m.group_id
                RIGHT JOIN user u on m.user_id = u.id
                WHERE (:groupId IS NULL OR ehg.group_id = :groupId)
                  AND (:eventType IS NULL OR event_type_id = :eventType)
                  AND u.username = :username
                ORDER BY event.`from` 
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->bindValue(':eventType', $eventTypeId, PDO::PARAM_INT);
        $stmt->bindParam(':username', $user->username);

        return $this->fetchAllEvents($stmt);
    }

    /**
     * @param int $groupId
     * @param int $eventTypeId
     * @param int $limit
     * @param User $user
     * @param int $offset
     * @return Event[]
     */
    public function getEventsByGroupAndEventTypeWithPagination(?int $groupId, ?int $eventTypeId, int $limit, User $user, int $offset = 0): array
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
                RIGHT JOIN `group` g on ehg.group_id = g.id
                RIGHT JOIN membership m on g.id = m.group_id
                RIGHT JOIN user u on m.user_id = u.id
                WHERE (:groupId IS NULL OR ehg.group_id = :groupId)
                  AND (:eventType IS NULL OR event_type_id = :eventType)
                  AND u.username = :username
                ORDER BY event.`from` 
                LIMIT :lim OFFSET :off
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->bindValue(':eventType', $eventTypeId, PDO::PARAM_INT);
        $stmt->bindParam(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':off', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':username', $user->username);


        return $this->fetchAllEvents($stmt);
    }

    /**
     * Get all events with offset.
     * @param int $groupId
     * @param int $eventTypeId
     * @param User $user
     * @param int $offset
     * @return Event[]
     */
    public function getEventByGroupAndEventWithOffset(int $groupId, int $eventTypeId, User $user, int $offset = 0): array
    {
        $events = $this->getEventsByGroupAndEventType($groupId, $eventTypeId, $user);
        return array_slice($events, $offset);
    }

    /**
     * Get event by id
     * @param int $eventId
     * @return Optional
     */
    public function getEventById(int $eventId)
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
                WHERE event.id = :eventId
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':eventId', $eventId, PDO::PARAM_INT);
        $stmt->execute();

        $event = $stmt->fetch();
        if ($event === false) {
            return Optional::failure();
        } else {
            return Optional::success($this->fetchEvent($event));
        }

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
            $events[$key] = $this->fetchEvent($val);
        }

        return $events;
    }

    /**
     * Parse array to return a event
     * @param array $val
     * @return Event
     */
    private function fetchEvent(array $val): Event
    {
        $event = new Event($val);
        $event->eventType = $this->eventTypeService->getEventTypeByEvent($val['eventTypeId']);
        $optional = $this->registrationStateService->getRegistrationTypeById($val['defaultRegistrationState']);
        if ($optional->isSuccess()) {
            $event->defaultRegistrationState = $optional->getData();
        }
        $event->groups = $this->groupService->getGroupsByEventId($val['id']);
        return $event;
    }

}
