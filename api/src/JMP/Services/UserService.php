<?php


namespace JMP\Services;


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

    public function createUser(array $user)
    {
        $sql = <<<SQL
INSERT INTO user
(username, lastname, firstname, email, token, password, password_change) 
VALUES (:username, :lastname, :firstname, :email, :token, :password, :password_change)
SQL;


        return new \JMP\Models\User(
            $user
        );
    }
}