<?php

namespace JMP\Models;


class RegistrationState implements ArrayConvertable
{
    use ArrayConvertableTrait;
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
        $this->id = (int)$args['id'];
        $this->name = $args['name'];
        $this->reasonRequired = $args['reasonRequired'] ? true : false;
    }
}