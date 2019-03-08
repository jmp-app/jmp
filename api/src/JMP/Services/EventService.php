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
     * @var User
     */
    private $user;

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
        $this->user = $container->get('user');
    }

    /**
     * @param int $groupId
     * @param int $eventTypeId
     * @param bool $getAll
     * @param bool $getElapsed
     * @param User $user
     * @return Event[]
     * @throws \Exception
     */
    public function getEventsByGroupAndEventType(?int $groupId, ?int $eventTypeId, bool $getAll, bool $getElapsed, User $user): array
    {
        $sql = <<< SQL
                SELECT DISTINCT event.id,
                       event.title,
                       event.description,
                       `from`,
                       `to`,
                       place,
                       event_type_id AS eventTypeId,
                       default_registration_state_id AS defaultRegistrationState
                FROM event
                LEFT JOIN event_has_group ehg ON event.id = ehg.event_id
                LEFT JOIN event_type ON event.event_type_id = event_type.id
                LEFT JOIN `group` g ON ehg.group_id = g.id
                LEFT JOIN membership m ON g.id = m.group_id
                LEFT JOIN user u ON m.user_id = u.id
                WHERE (:groupId IS NULL OR ehg.group_id = :groupId)
                  AND (:eventType IS NULL OR event_type_id = :eventType)
                  AND (:getAll IS TRUE OR :username = u.username)
                  AND (:elapsed IS TRUE OR event.`to` >= NOW())
                ORDER BY event.`from`, event.`to` 
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->bindValue(':eventType', $eventTypeId, PDO::PARAM_INT);
        $stmt->bindParam(':username', $user->username);
        $stmt->bindParam(':getAll', $getAll);
        $stmt->bindParam(':elapsed', $getElapsed);

        return $this->fetchAllEvents($stmt);
    }

    /**
     * @param int $groupId
     * @param int $eventTypeId
     * @param int $limit
     * @param bool $getAll
     * @param bool $getElapsed
     * @param User $user
     * @param int $offset
     * @return Event[]
     * @throws \Exception
     */
    public function getEventsByGroupAndEventTypeWithPagination(?int $groupId, ?int $eventTypeId, int $limit, bool $getAll, bool $getElapsed, User $user, int $offset = 0): array
    {
        $sql = <<< SQL
                SELECT DISTINCT event.id,
                       event.title,
                       event.description,
                       `from`,
                       `to`,
                       place,
                       event_type_id AS eventTypeId,
                       default_registration_state_id AS defaultRegistrationState
                FROM event
                LEFT JOIN event_has_group ehg ON event.id = ehg.event_id
                LEFT JOIN event_type ON event.event_type_id = event_type.id
                LEFT JOIN `group` g ON ehg.group_id = g.id
                LEFT JOIN membership m ON g.id = m.group_id
                LEFT JOIN user u ON m.user_id = u.id
                WHERE (:groupId IS NULL OR ehg.group_id = :groupId)
                  AND (:eventType IS NULL OR event_type_id = :eventType)
                  AND (:getAll IS TRUE OR :username = u.username)
                  AND (:elapsed IS TRUE OR event.`to` >= NOW())
                ORDER BY event.`from`, event.`to` 
                LIMIT :lim OFFSET :off
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->bindValue(':eventType', $eventTypeId, PDO::PARAM_INT);
        $stmt->bindParam(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':off', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':username', $user->username);
        $stmt->bindParam(':getAll', $getAll);
        $stmt->bindParam(':elapsed', $getElapsed);

        return $this->fetchAllEvents($stmt);
    }

    /**
     * Get all events with offset.
     * @param int $groupId
     * @param int $eventTypeId
     * @param bool $getAll
     * @param bool $getElapsed
     * @param User $user
     * @param int $offset
     * @return Event[]
     * @throws \Exception
     */
    public function getEventByGroupAndEventWithOffset(int $groupId, int $eventTypeId, bool $getAll, bool $getElapsed, User $user, int $offset = 0): array
    {
        $events = $this->getEventsByGroupAndEventType($groupId, $eventTypeId, $getAll, $getElapsed, $user);
        return array_slice($events, $offset);
    }

    /**
     * Get event by id
     * @param int $eventId
     * @param User $user
     * @return Optional
     * @throws \Exception
     */
    public function getEventById(int $eventId, User $user)
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
                LEFT JOIN event_has_group ehg ON event.id = ehg.event_id
                LEFT JOIN event_type ON event.event_type_id = event_type.id
                LEFT JOIN `group` g ON ehg.group_id = g.id
                LEFT JOIN membership m ON g.id = m.group_id
                LEFT JOIN user u ON m.user_id = u.id
                WHERE event.id = :eventId
                  AND (:isAdmin IS TRUE OR u.username = :username)
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':eventId', $eventId, PDO::PARAM_INT);
        $stmt->bindValue(':isAdmin', $user->isAdmin, PDO::PARAM_BOOL);
        $stmt->bindValue(':username', $user->username);
        $stmt->execute();

        $event = $stmt->fetch();
        if ($event === false) {
            return Optional::failure();
        } else {
            return Optional::success($this->fetchEvent($event));
        }

    }

    /**
     * Creates a new Event
     * @param array $params Contains all required fields
     * @return Optional
     * @throws \Exception
     */
    public function createEvent(array $params): Optional
    {
        $this->db->beginTransaction();
        try {
            if ($this->insertEvent($params) === false) {
                $this->db->rollBack();
                return Optional::failure();
            }

            $eventId = $this->getLastInsertedEventId();
            if ($eventId === false) {
                $this->db->rollBack();
                return Optional::failure();
            }

            $eventId = $eventId['id'];
            if ($this->addGroupsToEvent($params, $eventId) === false) {
                $this->db->rollBack();
                return Optional::failure();
            }

            // Return the fully created event
            $optional = $this->getEventById($eventId, $this->user);

            // Everything went well, do a commit
            $this->db->commit();
            return $optional;
        } catch (\PDOException $exception) {
            // Something went wrong, do a rollback
            $this->db->rollBack();
            return Optional::failure();
        }
    }

    /**
     * Checks if an event exists
     * @param int $eventId
     * @return bool
     */
    public function eventExists(int $eventId): bool
    {
        $sql = <<< SQL
SELECT *
FROM jmp.event
WHERE id = :eventId
SQL;
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Executes the statement and parses its result to return a list of events.
     * @param \PDOStatement $stmt
     * @return Event[]
     * @throws \Exception
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
     * @throws \Exception
     */
    private function fetchEvent(array $val): Event
    {
        $event = new Event($val);
        $optional = $this->eventTypeService->getEventTypeByEvent($val['eventTypeId']);
        if ($optional->isSuccess()) {
            $event->eventType = $optional->getData();
        }
        $optional = $this->registrationStateService->getRegistrationTypeById($val['defaultRegistrationState']);
        if ($optional->isSuccess()) {
            $event->defaultRegistrationState = $optional->getData();
        }
        $event->groups = $this->groupService->getGroupsByEventId($val['id']);
        return $event;
    }

    /**
     * @param array $params
     * @return bool
     */
    private function insertEvent(array $params): bool
    {
        $sql = <<< SQL
INSERT INTO `jmp`.`event` (`title`, `from`, `to`, `place`, `description`, `event_type_id`,
           `default_registration_state_id`)
VALUES (:title, :from, :to, :place, :description, :eventType, :defaultRegistrationState);
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':title', $params['title']);
        $stmt->bindParam(':from', $params['from']);
        $stmt->bindParam(':to', $params['to']);
        $stmt->bindParam(':place', $params['place']);
        $stmt->bindParam(':description', $params['description']);
        $stmt->bindParam(':eventType', $params['eventType']);
        $stmt->bindParam(':defaultRegistrationState', $params['defaultRegistrationState']);

        // Insert event
        return $stmt->execute();
    }

    /**
     * @return mixed
     */
    private function getLastInsertedEventId()
    {
        $sql = <<< SQL
SELECT LAST_INSERT_ID() as id;
SQL;

        $stmt = $this->db->prepare($sql);
        // Gets the ID of the inserted event
        $stmt->execute();

        $eventId = $stmt->fetch();
        return $eventId;
    }

    /**
     * @param array $params
     * @param $eventId
     * @return bool
     */
    private function addGroupsToEvent(array $params, $eventId): bool
    {
// Adds all groups to the event
        $successful = [];
        foreach ($params['groups'] as $groupId) {
            $success = $this->groupService->addGroupToEvent($groupId, $eventId);
            array_push($successful, $success);
        }
        return !in_array(false, $successful, true);
    }

}
