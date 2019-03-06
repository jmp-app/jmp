<?php

namespace JMP\Services;

use JMP\Utils\Optional;
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
        $this->logger = $container->get('logger');
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
     * @return Optional
     */
    public function addUsersToGroup(int $groupId, array $users): Optional
    {
        $sql = <<< SQL
            INSERT INTO membership (group_id, user_id) 
            VALUES (:groupId, :userId)
            ON DUPLICATE KEY UPDATE group_id=group_id, user_id=user_id
SQL;

        return $this->executeForEachUser($sql, $groupId, $users);
    }

    /**
     * Executes sql against each user. The groupId and userId are bound to the prepared statement.
     * @param string $sql
     * @param int $groupId
     * @param array $users
     * @return Optional
     */
    private function executeForEachUser(string $sql, int $groupId, array $users): Optional
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare($sql);

            foreach ($users as $userId) {
                $stmt->bindValue(':groupId', $groupId, \PDO::PARAM_INT);
                $stmt->bindValue(':userId', $userId, \PDO::PARAM_INT);

                $success = $stmt->execute();
                if ($success === false) {
                    throw new \Exception('Failed to insert into membership with groupId: ' . $groupId .
                        ' and userId: ' . $userId . '. ' . $sql);
                }
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            $this->logger->error($e->getMessage());
            return Optional::failure();
        }

        return Optional::success(true);
    }

    /**
     * Creates a membership for each user with the group
     * @param int $groupId
     * @param array $users
     * @return Optional
     * @throws \Exception
     */
    public function removeUsersFromGroup(int $groupId, array $users): Optional
    {
        $sql = <<< SQL
            DELETE FROM membership 
            WHERE group_id = :groupId AND user_id = :userId
SQL;

        return $this->executeForEachUser($sql, $groupId, $users);
    }

}
