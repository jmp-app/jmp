<?php

namespace JMP\Services;

use Psr\Container\ContainerInterface;

class MembershipService
{

    /**
     * @var \PDO
     */
    protected $db;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * EventService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
    }

    public function deleteMemberships(int $groupId): void
    {
        $sql = <<< SQL
            DELETE FROM `membership`
            WHERE group_id = :groupId;
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':groupId', $groupId, \PDO::PARAM_INT);
        $stmt->execute();
    }

}
