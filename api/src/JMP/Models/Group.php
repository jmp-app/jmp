<?php

namespace JMP\Models;


class Group implements ArrayConvertable
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

    /**
     * Returns an array representing the current object.
     * This method makes it possible to do some conversion or filtering before casting the object to an array
     * @return array
     */
    public function toArray(): array
    {
        return array_filter((array)$this, function ($value) {
            return $value !== null;
        });
    }
}