<?php


namespace JMP\Services;


use JMP\Models\User;
use Psr\Container\ContainerInterface;

class UserService
{
    /**
     * @var \PDO
     */
    protected $db;

    /**
     * EventService constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->db = $container->get('database');
    }

    /**
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
     * @param $username
     * @return User
     */
    private function getUserByUsername(string $username): User
    {
        $sql = <<<SQL
SELECT username, lastname, firstname, email, password_change AS passwordChange
FROM user
WHERE username = :username
SQL;


        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':username', $username);

        $stmt->execute();

        return new User($stmt->fetch());
    }
}