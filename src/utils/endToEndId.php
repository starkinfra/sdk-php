<?php

namespace StarkInfra\Utils;


class EndToEndId
{
    public static function create($ispb)
    {
        $source = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $randLength = strlen($source)-1;
        $endToEndId = "E" . $ispb . date('YmdHi');
        for ($i = 0; $i <= 10; $i++) {
            $endToEndId .= $source[rand(0, $randLength)];
        }
        return $endToEndId;
    }
}

?>