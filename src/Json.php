<?php

namespace Kafene\Json;

class Json
{
    public static function encode($input = null)
    {
        return new Encoder($input);
    }

    public static function decode(string $input = '')
    {
        return new Decoder($input);
    }

    public static function lastError(): object
    {
        return (object) [
            'code'    => json_last_error(),
            'message' => json_last_error_msg(),
        ];
    }
}
