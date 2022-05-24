<?php

namespace StarkInfra\Utils;

class BacenId
{
    public static function create($bankCode)
    {
        $source = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $randLength = strlen($source)-1;
        $bacenId = $bankCode . date('YmdHi');
        for ($i = 0; $i <= 10; $i++) {
            $bacenId .= $source[rand(0, $randLength)];
        }
        return $bacenId;
    }
}
