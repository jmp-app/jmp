<?php

namespace JMP\Models;

class Registration implements ArrayConvertable
{
    use ArrayConvertableTrait;
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
}