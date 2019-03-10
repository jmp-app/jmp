<?php

namespace JMP\Models;


use JMP\Utils\Converter;

trait ArrayConvertableTrait
{
    /**
     * Returns an array representing the current object.
     * This method makes it possible to do some conversion or filtering before casting the object to an array
     * @return array
     */
    public function toArray(): array
    {
        return Converter::convertArray((array)$this);
    }


}