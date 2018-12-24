<?php

namespace JMP\Models;

class Registration implements ArrayConvertable
{
    /**
     * @var int
     */
    public $eventId;

    /**
     * @var int
     */
    public $userId;

    /**
     * @var string
     */
    public $reason;

    /**
     * @var RegistrationState
     */
    public $registrationState;

    /**
     * Registration constructor.
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->eventId = (int)$args['eventId'];
        $this->userId = (int)$args['userId'];
        $this->reason = $args['reason'];
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