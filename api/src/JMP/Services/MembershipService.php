<?php

namespace JMP\Services;

use Monolog\Logger;
use Psr\Container\ContainerInterface;

class MembershipService
{

    /**
     * @var \PDO
     */
    protected $db;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * EventService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
        $this->db = $container->get('logger');
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
    public function addUsersToGroup(int $groupId, array $users): bool
    {
        $sql = <<< SQL
            INSERT INTO membership (group_id, user_id) 
            VALUES (:groupId, :userId)
SQL;

        return $this->executeForEachUser($sql, $groupId, $users);
    }

    /**
     * Creates a membership for each user with the group
     * @param int $groupId
     * @param array $users
     */
    public function removeUsersFromGroup(int $groupId, array $users): bool
    {
        $sql = <<< SQL
            DELETE FROM membership 
            WHERE group_id = :groupId AND user_id = :userId
SQL;

        return $this->executeForEachUser($sql, $groupId, $users);
    }

    /**
     * Executes sql against each user. The groupId and userId are bound to the prepared statement.
     * @param string $sql
     * @param int $groupId
     * @param array $users
     * @return bool If all executions were successfull
     * @throws \Exception
     */
    private function executeForEachUser(string $sql, int $groupId, array $users): bool
    {
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
            $this->logger->error($e->getMessage());
            return false;
        }

        return true;
    }

}
