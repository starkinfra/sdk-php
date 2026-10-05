<?php

namespace StarkInfra\Utils;

class PixSubscriptionBacenId
{
    public static function create($bankCode, $prefix)
    {
        return $prefix . BacenId::create($bankCode, 'Ymd');
    }
}
