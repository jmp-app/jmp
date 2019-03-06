<?php


namespace JMP\Models;


class User implements ArrayConvertable
{
    use ArrayConvertableTrait;
    /**
     * @var int
     */
    public $id;
    /**
     * @var string
     */

    public $username;
    /**
     * @var string
     */

    public $lastname;
    /**
     * @var string
     */
    public $firstname;
    /**
     * @var string
     */
    public $password;
    /**
     * @var string
     */
    public $email;
    /**
     * @var bool
     */
    public $passwordChange;
    /**
     * @var bool
     */
    public $isAdmin;

    /**
     * User constructor.
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->id = (int)$args['id'];
        $this->username = $args['username'];
        $this->lastname = $args['lastname'];
        $this->firstname = $args['firstname'];
        $this->password = $args['password'];
        $this->email = $args['email'];
        $this->passwordChange = (bool)$args['passwordChange'];
        $this->isAdmin = (bool)$args['isAdmin'];
    }

    public function __toString(): string
    {
        unset($this->password);
        return (string)$this->toArray();
    }
}
