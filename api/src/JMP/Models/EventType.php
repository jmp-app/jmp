<?php

namespace JMP\Models;


class EventType implements ArrayConvertable
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $color;

    /**
     * EventType constructor.
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->id = (int)$args['id'];
        $this->title = $args['title'];
        $this->color = $args['color'];
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