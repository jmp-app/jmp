<?php

namespace jmp\Services;

use jmp\Models\User;
use jmp\Utils\Optional;
use PDO;
use Psr\Container\ContainerInterface;

class UserService
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
     * Select a user by its id
     * @param int $userId
     * @return Optional containing a User on succeed
     */
    public function getUserByUserId(int $userId): Optional
    {
        $optional = $this->getFullUserByUserId($userId);

        if ($optional->isFailure()) {
            return Optional::failure();
        }

        /** @var User $user */
        $user = $optional->getData();
        unset($user->password);

        return Optional::success($user);
    }

    /**
     * Select all data of a user by its id
     * @param int $userId
     * @return Optional
     */
    private function getFullUserByUserId(int $userId): Optional
    {
        $sql = <<<SQL
SELECT user.id, username, lastname, firstname, email, is_admin AS isAdmin, password_change AS passwordChange, password
FROM user
WHERE id = :userId
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);

        $stmt->execute();

        $user = $stmt->fetch();

        if ($user === false) {
            return Optional::failure();
        } else {
            return Optional::success(new User($user));
        }
    }

    /**
     * Creates a new user and returns the created one by @param User $user
     * @return Optional
     * @uses UserService::getUserByUsername()
     * The given user is saved as is. E.g password hashing must be done in advance
     */
    public function createUser(User $user): Optional
    {
        $sql = <<<SQL
INSERT INTO user
(username, lastname, firstname, email, password, password_change, is_admin) 
VALUES (:username, :lastname, :firstname, :email, :password, :password_change, :is_admin)
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':username', $user->username);
        $stmt->bindValue(':lastname', $user->lastname, PDO::PARAM_STR);
        $stmt->bindValue(':firstname', $user->firstname, PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $user->password);
        $stmt->bindParam(':password_change', $user->passwordChange, PDO::PARAM_BOOL);
        $stmt->bindParam(':is_admin', $user->isAdmin, PDO::PARAM_BOOL);


        $successful = $stmt->execute();

        if ($successful === false) {
            return Optional::failure();
        }

        return $this->getUserByUsername($user->username);

    }

    /**
     * Checks whether a user with the given username already exists, as it must be unique
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
     * Checks whether a user with the specified id exists
     * @param int $id
     * @return bool
     */
    public function userExists(int $id): bool
    {
        $sql = <<< SQL
            SELECT id
            FROM user
            where id = :id
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Returns the user with the given username.
     * The password isn't returned.
     * Null fields are returned
     * @param $username
     * @return Optional
     */
    private function getUserByUsername(string $username): Optional
    {
        $sql = <<<SQL
SELECT user.id, username, lastname, firstname, email, password_change as passwordChange, is_admin AS isAdmin
FROM user
WHERE username = :username
SQL;


        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':username', $username);

        $stmt->execute();

        $data = $stmt->fetch();

        if ($data === false) {
            return Optional::failure();
        }

        return Optional::success(new User($data));
    }

    /**
     * @param int|null $groupId
     * @return User[]
     */
    public function getUsers(?int $groupId): array
    {
        $sql = <<< SQL
            SELECT DISTINCT user.id, username, lastname, firstname, email, is_admin as isAdmin
            FROM user
                LEFT JOIN membership m on user.id = m.user_id
            WHERE (:groupId IS NULL OR m.group_id = :groupId)
SQL;

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->execute();

        $users = $stmt->fetchAll();

        if ($users === false) {
            return array();
        }

        foreach ($users as $key => $val) {
            $users[$key] = new User($val);
        }

        return $users;
    }

    /**
     * Change the password of a user, fails if the current password is incorrect
     * @param int $userId
     * @param string $newPassword
     * @return bool
     */
    public function changePassword(int $userId, string $newPassword): bool
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = <<< SQL
            UPDATE user
            SET password = :newPassword
            WHERE id = :userId
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':newPassword', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Updates a user's properties with the given $updates
     * @param int $id The id must exist
     * @param array $updates
     * @return Optional
     */
    public function updateUser(int $id, array $updates): Optional
    {
        $optional = $this->getFullUserByUserId($id);
        if ($optional->isFailure()) {
            return Optional::failure();
        }

        /** @var User $user */
        $user = $optional->getData();

        $sql = <<< SQL
            UPDATE user
            SET username = :username, firstname = :firstname, lastname = :lastname, email = :email, password = :password,
                is_admin = :isAdmin, password_change = :passwordChange
            WHERE id = :id;
SQL;

        $updatedUser = $this->getUpdatedUser($updates, $user);

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':username', $updatedUser->username);
        $stmt->bindValue(':firstname', $updatedUser->firstname);
        $stmt->bindValue(':lastname', $updatedUser->lastname);
        $stmt->bindValue(':email', $updatedUser->email);
        $stmt->bindValue(':password', $updatedUser->password);
        $stmt->bindValue(':isAdmin', $updatedUser->isAdmin, PDO::PARAM_BOOL);
        $stmt->bindValue(':passwordChange', $updatedUser->passwordChange, PDO::PARAM_BOOL);

        $successful = $stmt->execute();
        if ($successful === false) {
            return Optional::failure();
        }

        return $this->getFullUserByUserId($id);
    }

    /**
     * @param $users
     * @return array
     */
    public function groupUsersByValidity(array $users): array
    {
        // Remove all duplicates
        array_unique($users);

        // Check which users are valid to join
        $existingUsers = [];
        $notExistingUsers = [];
        foreach ($users as $user) {
            if ($this->userExists($user)) {
                // user exists
                array_push($existingUsers, $user);
            } else {
                // user doesnt exist
                array_push($notExistingUsers, (int)$user);
            }
        }
        return array($existingUsers, $notExistingUsers);
    }

    /**
     * Update user with the given updates (key / value pairs)
     * @param array $userUpdates
     * @param User $currentUser
     * @return User
     */
    private function getUpdatedUser(array $userUpdates, User $currentUser): User
    {
        $username = $userUpdates['username'];
        $lastname = $userUpdates['lastname'];
        $firstname = $userUpdates['firstname'];
        $email = $userUpdates['email'];
        $password = $userUpdates['password'];
        $passwordChange = $userUpdates['passwordChange'];
        $isAdmin = $userUpdates['isAdmin'];

        if (!is_null($username)) {
            $currentUser->username = $username;
        }
        if (!is_null($lastname)) {
            $currentUser->lastname = $lastname;
        }
        if (!is_null($firstname)) {
            $currentUser->firstname = $firstname;
        }
        if (!is_null($email)) {
            $currentUser->email = $email;
        }
        if (!is_null($password)) {
            $currentUser->password = password_hash($userUpdates['password'], PASSWORD_DEFAULT);
        }
        if (!is_null($passwordChange)) {
            $currentUser->passwordChange = $passwordChange;
        }
        if (!is_null($isAdmin)) {
            $currentUser->isAdmin = $isAdmin;
        }

        return $currentUser;
    }

    /**
     * Delete User
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        // User
        $sql = <<< SQL
            DELETE FROM user
            WHERE id = :id;
SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

}
