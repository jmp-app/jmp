<?php

namespace jmp\Models;


interface ArrayConvertable
{
    /**
     * Returns an array representing the current object.
     * This method makes it possible to do some conversion or filtering before casting the object to an array
     * @return array
     */
    public function toArray(): array;
}