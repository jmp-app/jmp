<?php

namespace jmp\Models;


class Group implements ArrayConvertable
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
     * @var User[]
     */
    public $users;

    /**
     * Group constructor.
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->id = (int)$args['id'];
        $this->name = $args['name'];
    }
}