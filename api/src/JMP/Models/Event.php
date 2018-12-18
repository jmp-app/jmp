<?php

namespace JMP\Models;


class Event implements ArrayConvertable
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
    public $from;

    /**
     * @var string
     */
    public $to;

    /**
     * @var string
     */
    public $place;

    /**
     * @var string
     */
    public $description;

    /**
     * @var EventType;
     */
    public $eventType;

    /**
     * @var RegistrationState
     */
    public $defaultRegistrationState;

    /**
     * @var Group[]
     */
    public $groups;

    /**
     * Event constructor.
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->id = (int)$args['id'];
        $this->title = $args['title'];
        $this->from = $args['from'];
        $this->to = $args['to'];
        $this->place = $args['place'];
        $this->description = $args['description'];
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