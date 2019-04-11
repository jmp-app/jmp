<?php


namespace jmp\Utils;


use jmp\Models\ArrayConvertable;

class Converter
{

    /**
     * Converts an array of @uses ArrayConvertable into an array of arrays
     * @param array $data
     * @return array
     */
    public static function convertArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = Converter::convertArray($value);
            } elseif ($value === null) {
                unset($data[$key]);
            } elseif ($value instanceof ArrayConvertable) {
                $data[$key] = $value->toArray();
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * Converts a @uses ArrayConvertable into an array
     * @param ArrayConvertable $var
     * @return array
     */
    public static function convert(ArrayConvertable $var): array
    {
        return $var->toArray();
    }

}
