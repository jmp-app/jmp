<?php


namespace jmp\Models;


class Presence implements ArrayConvertable
{
    use ArrayConvertableTrait;

    /**
     * @var int
     */
    public $event;

    /**
     * @var int
     */
    public $user;

    /**
     * @var int
     */
    public $auditor;

    /**
     * @var bool
     */
    public $hasAttended;

    /**
     * Presence constructor.
     */
    public function __construct(array $args)
    {
        $this->event = (int)$args['event'];
        $this->user = (int)$args['user'];
        $this->auditor = (int)$args['auditor'];
        $this->hasAttended = $args['hasAttended'] === '1' || $args['hasAttended'] === true ? true : false;
    }


}
