<?php

namespace JMP\Services;


use JMP\Models\EventType;
use JMP\Utils\Optional;
use Psr\Container\ContainerInterface;

class EventTypeService
{
    /**
     * @var \PDO
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
     * @return Optional
     */
    public function getAllEventTypes(): Optional
    {
        $sql = <<< SQL
SELECT *
FROM jmp.event_type
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $eventTypes = $stmt->fetchAll();
        if ($eventTypes === false) {
            return Optional::failure();
        }

        // Convert array to model objects
        foreach ($eventTypes as $key => $value) {
            $eventTypes[$key] = new EventType($value);
        }

        return Optional::success($eventTypes);
    }

    /**
     * @param EventType $eventType
     * @return Optional
     */
    public function createEventType(EventType $eventType): Optional
    {
        $sql = <<< SQL
INSERT INTO jmp.event_type
(jmp.event_type.title, jmp.event_type.color) 
VALUES (:title, :color)
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':title', $eventType->title);
        $stmt->bindParam(':color', $eventType->color);

        if ($stmt->execute() === false) {
            return Optional::failure();
        }

        $id = $this->getLastInsertedEventTypeId();
        if ($id === false) {
            return Optional::failure();
        }

        return $this->getEventTypeById($id['id']);
    }

    /**
     * @return mixed
     */
    private function getLastInsertedEventTypeId()
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
     * @param int $eventTypeId
     * @return Optional
     */
    public function getEventTypeByEvent(int $eventTypeId)
    {
        $sql = <<< SQL
SELECT *
FROM event_type
WHERE id = :eventTypeId
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':eventTypeId', $eventTypeId);
        $stmt->execute();
        $eventType = $stmt->fetch();
        if ($eventType === false) {
            return Optional::failure();
        } else {
            return Optional::success(new EventType($eventType));
        }
    }

    /**
     * Checks whether an event type with the given id already exists
     * @param int $eventTypeId
     * @return bool
     */
    public function eventTypeExists(int $eventTypeId): bool
    {
        $sql = <<<SQL
            SELECT id
            FROM jmp.`event_type`
            WHERE id = :id
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $eventTypeId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * @param int $id
     * @return Optional
     */
    public function getEventTypeById(int $id): Optional
    {
        $sql = <<<SQL
SELECT *
FROM jmp.event_type
WHERE id = :id
LIMIT 1
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $result = $stmt->fetch();
        if ($result === false) {
            return Optional::failure();
        }

        return Optional::success(new EventType($result));
    }
}
