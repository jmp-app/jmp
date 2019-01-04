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

    /**
     * Deletes all memberships that exist between any user and the specified group
     * @param int $groupId
     */
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

    /**
     * Creates a membership for each user with the group
     * @param int $groupId
     * @param array $users
     */
    public function addUsersToGroup(int $groupId, array $users): void
    {
        $sql = <<< SQL
            INSERT INTO membership (group_id, user_id) 
            VALUES (:groupId, :userId)
SQL;

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare($sql);

            foreach ($users as $userId) {
                $stmt->bindValue(':groupId', $groupId, \PDO::PARAM_INT);
                $stmt->bindValue(':userId', $userId, \PDO::PARAM_INT);

                $success = $stmt->execute();
                if (!$success) {
                    throw new \Exception('Failed to insert into membership with groupId: ' . $groupId .
                        ' and userId: ' . $userId . '. ' . $sql);
                }
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
        }

    }

}
