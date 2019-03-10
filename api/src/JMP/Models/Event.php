<?php

namespace JMP\Models;


class Event implements ArrayConvertable
{
    use ArrayConvertableTrait;
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
     * @throws \Exception
     */
    public function __construct(array $args)
    {
        $this->id = (int)$args['id'];
        $this->title = $args['title'];
        $this->from = $this->convertDateTime($args['from']);
        $this->to = $this->convertDateTime($args['to']);
        $this->place = $args['place'];
        $this->description = $args['description'];
    }

    /**
     * @param $dateTime
     * @return String ISO-Format
     * @throws \Exception
     */
    public function convertDateTime($dateTime): String
    {
        $dateTime = new \DateTime($dateTime);
        return $dateTime->format('Y-m-d\TH:i');
    }

    /**
     * @param $dateTime
     * @return String ISO-Format
     * @throws \Exception
     */
    public function convertDateTime($dateTime): String
    {
        $dateTime = new \DateTime($dateTime);
        return $dateTime->format('Y-m-d\TH:i');
    }
}