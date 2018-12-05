<?php
/**
 * Created by PhpStorm.
 * User: dominik
 * Date: 04.12.18
 * Time: 18:30
 */

namespace JMP\Services;


use JMP\Models\Group;
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
LEFT JOIN event_has_group ehg on `group`.id = ehg.group_id
WHERE ehg.event_id = :eventId
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':eventId', $eventId);
        $stmt->execute();

        $groups = $stmt->fetchAll();

        foreach ($groups as $key => $group) {
            $groups[$key] = new Group($group);
        }
        return $groups;
    }
}