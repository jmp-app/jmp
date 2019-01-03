<?php


namespace JMP\Services;


use JMP\Models\User;
use PDO;
use Psr\Container\ContainerInterface;

class UserService
{
    /**
     * @var \PDO
     */
    protected $db;
    /**
     * @var string
     */
    protected $adminGroupName;

    /**
     * EventService constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
        $this->adminGroupName = $container->get('settings')['auth']['adminGroupName'];
    }

    /**
     * Creates a new user and returns the created one by @uses UserService::getUserByUsername()
     * The given user is saved as is. E.g password hashing must be done in advance
     * @param User $user
     * @return User
     */
    public function createUser(User $user): User
    {
        $sql = <<<SQL
INSERT INTO user
(username, lastname, firstname, email, password, password_change) 
VALUES (:username, :lastname, :firstname, :email, :password, :password_change)
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':username', $user->username);
        $stmt->bindValue(':lastname', $user->lastname, \PDO::PARAM_STR);
        $stmt->bindValue(':firstname', $user->firstname, \PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->email, \PDO::PARAM_STR);
        $stmt->bindParam(':password', $user->password);
        $stmt->bindValue(':password_change', 1, \PDO::PARAM_INT);


        $stmt->execute();

        return $this->getUserByUsername($user->username);

    }

    /**
     * Checks wheter a user with the given username alredy exists, as it have to be unique
     * @param string $username
     * @return bool
     */
    public function isUsernameUnique(string $username): bool
    {
        $sql = <<<SQL
SELECT user.username
FROM user
WHERE username = :username
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':username', $username);

        $stmt->execute();

        return $stmt->rowCount() < 1;

    }

    /**
     * Returns the user with the given username.
     * The password isn't returned.
     * Null fields are returned
     * @param $username
     * @return User
     */
    private function getUserByUsername(string $username): User
    {
        $sql = <<<SQL
SELECT user.id, username, lastname, firstname, email,
#        Check if the user is an admin, 1-> admin, 0-> no admin
       NOT ISNULL((SELECT username
                   FROM user
                          LEFT JOIN membership m ON user.id = m.user_id
                          LEFT JOIN `group` g ON m.group_id = g.id
                   WHERE username = :username
                     AND g.name = :adminGroupName
       )) AS isAdmin
FROM user
WHERE username = :username
SQL;


        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':adminGroupName', $this->adminGroupName);

        $stmt->execute();

        return new User($stmt->fetch());
    }

    /**
     * @param int|null $groupId
     * @return User[]
     */
    public function getUsers(?int $groupId): array
    {
        $sql = <<< SQL
            SELECT DISTINCT user.id, username, lastname, firstname, email, is_admin
            FROM user
                LEFT JOIN membership m on user.id = m.user_id
            WHERE (:groupId IS NULL OR m.group_id = :groupId)
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->execute();

        $users = $stmt->fetchAll();

        foreach ($users as $key => $val) {
            $users[$key] = new User($val);
        }

        return $users;
    }

}