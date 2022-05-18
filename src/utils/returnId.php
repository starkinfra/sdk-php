<?php

namespace StarkInfra\Utils;

class ReturnId
{
    public static function create($bankCode)
    {
        $returnId = "D".BacenId::create($bankCode);
        return $returnId;
    }
}
