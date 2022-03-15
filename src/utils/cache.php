<?php

namespace StarkInfra\Utils;


class Cache
{
    static private $starkPublicKey;

    static function getStarkPublicKey()
    {
        return self::$starkPublicKey;
    }
    
    static function setStarkPublicKey($publicKey)
    {
        self::$starkPublicKey = $publicKey;
    }
}
