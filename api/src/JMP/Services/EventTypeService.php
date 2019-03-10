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
}
