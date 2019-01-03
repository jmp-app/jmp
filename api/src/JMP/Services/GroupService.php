<?php

namespace JMP\Services;

use JMP\Models\Group;
use JMP\Utils\Optional;
use Psr\Container\ContainerInterface;

class GroupService
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
     * @param int $eventId
     * @return Group[]
     */
    public function getGroupsByEventId(int $eventId)
    {
        $sql = <<< SQL
            SELECT id, name
            FROM `group`
            LEFT JOIN event_has_group ehg ON `group`.id = ehg.group_id
            WHERE ehg.event_id = :eventId
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->execute();


        return $this->fetchGroups($stmt);
    }

    /**
     * @return Group[] containing all groups
     */
    public function getAllGroups() {
        $sql = <<< SQL
            SELECT id, name
            FROM `group`
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $this->fetchGroups($stmt);
    }

    /**
     * @param \PDOStatement $stmt the statement to use
     * @return Group[] array of groups
     */
    private function fetchGroups(\PDOStatement $stmt): array
    {
        $groups = $stmt->fetchAll();

        foreach ($groups as $key => $group) {
            $groups[$key] = new Group($group);
        }
        return $groups;
    }

}
