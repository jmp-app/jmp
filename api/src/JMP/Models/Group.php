<?php
/**
 * Created by PhpStorm.
 * User: dominik
 * Date: 04.12.18
 * Time: 16:31
 */

namespace JMP\Models;


class Group
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
     * Group constructor.
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->id = (int)$args['id'];
        $this->name = $args['name'];
    }
}