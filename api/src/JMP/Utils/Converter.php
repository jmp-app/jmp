<?php


namespace JMP\Utils;


use JMP\Models\ArrayConvertable;

class Converter
{

    /**
     * Converts an array of @uses ArrayConvertable into an array of arrays
     * If a value isn't an instance of @uses ArrayConvertable it is casted with (array)
     * @param array $data
     * @return array
     */
    public static function convertArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = self::convertArray($value);
            }
            if ($value instanceof ArrayConvertable) {
                $data[$key] = self::convert($value);
            } else {
                $data[$key] = (array)$value;
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
