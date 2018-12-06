<?php

namespace JMP\Models;


class RegistrationState
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $reasonRequired;

    /**
     * RegistrationState constructor.
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->id = $args['id'];
        $this->name = $args['name'];
        $this->reasonRequired = $args['reasonRequired'] === "0" ? false : true;
    }


}