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
     * @var UserService
     */
    protected $userService;

    /**
     * @var MembershipService
     */
    protected $membershipService;

    /**
     * EventService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
        $this->userService = $container->get('userService');
        $this->membershipService = $container->get('membershipService');
    }

    /**
     * @param string $name
     * @return Optional
     */
    public function createGroup(string $name): Optional
    {
        $sql = <<< SQL
            INSERT INTO `group` (name)
            VALUES (:name)
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        return $this->getGroupByName($name);
    }

    /**
     * @param $id
     * @return bool successful
     */
    public function deleteGroup(int $id): bool
    {
        $this->membershipService->deleteMemberships($id);

        $sql = <<< SQL
            DELETE FROM `group`
            WHERE id = :id;
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @param int $id
     * @param string $newName
     * @return Optional
     */
    public function updateGroup(int $id, string $newName): Optional
    {
        $sql = <<< SQL
            UPDATE `group`
            SET name = :newName
            WHERE id = :id
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':newName', $newName);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $successful = $stmt->execute();

        if ($successful === false) {
            return Optional::failure();
        }

        return $this->getGroupByName($newName);
    }

    /**
     * Get a group by its unique name
     * @param $name string <strong>You have to make sure that a group with that name exists</strong>
     * @return Optional
     */
    private function getGroupByName(string $name): Optional
    {
        $sql = <<< SQL
        SELECT id, name
        FROM `group`
        WHERE name = :name
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        $group = $stmt->fetch();

        if ($group === false) {
            return Optional::failure();
        } else {
            return $this->fetchGroup($group);
        }
    }

    /**
     * Checks whether a group with the given name already exists, as it must be unique
     * @param string $groupName
     * @return bool
     */
    public function isGroupNameUnique(string $groupName): bool
    {
        $sql = <<<SQL
            SELECT name
            FROM `group`
            WHERE name = :name
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $groupName);
        $stmt->execute();

        return $stmt->rowCount() < 1;
    }

    /**
     * Checks whether a group with the given id already exists
     * @param int $groupId
     * @return bool
     */
    public function groupExists(int $groupId): bool
    {
        $sql = <<<SQL
            SELECT id
            FROM `group`
            WHERE id = :id
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $groupId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * @param int $id
     * @return Optional
     */
    public function getGroupById(int $id): Optional
    {
        $sql = <<< SQL
            SELECT id, name
            FROM `group`
            WHERE id = :id
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $data = $stmt->fetch();

        if ($data === false) {
            return Optional::failure();
        }

        return Optional::success($this->fetchGroup($data));

    }

    /**
     * @param int $eventId
     * @return Group[]
     */
    public function getGroupsByEventId(int $eventId): array
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
    public function getAllGroups(): array
    {
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

        if ($groups === false) {
            return array();
        }

        foreach ($groups as $key => $group) {
            $optional = $this->fetchGroup($group);
            if ($optional->isSuccess()) {
                $groups[$key] = $optional->getData();
            }
        }
        return $groups;
    }

    /**
     * @param $group
     * @return Optional
     */
    private function fetchGroup(array $group): Optional
    {
        $group = new Group($group);
        $group->users = $this->userService->getUsers($group->id);
        return Optional::success($group);
    }

}
