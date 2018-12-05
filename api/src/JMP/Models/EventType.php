<?php
/**
 * Created by PhpStorm.
 * User: dominik
 * Date: 04.12.18
 * Time: 16:31
 */

namespace JMP\Models;


class EventType
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
}