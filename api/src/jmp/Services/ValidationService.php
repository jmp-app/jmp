<?php

namespace jmp\Services;

use PDO;
use Psr\Container\ContainerInterface;

class ValidationService
{
    /**
     * @var PDO
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
     * @param array $groupIds
     * @param int $userId
     * @return bool
     */
    public function isUserInOneOfTheGroups(array $groupIds, int $userId): bool
    {
        $sql = <<< SQL
SELECT DISTINCT u.id
FROM `group` g
       LEFT JOIN membership m on g.id = m.group_id
       LEFT JOIN user u on m.user_id = u.id
WHERE FIND_IN_SET(g.id, :groupIds)
AND u.id = :userId;
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':groupIds', implode(',', $groupIds));
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

}
