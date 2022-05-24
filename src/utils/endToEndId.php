<?php

namespace StarkInfra\Utils;


class EndToEndId
{
    public static function create($bankCode)
    {
        $endToEndId = "E" .BacenId::create($bankCode);
        return $endToEndId;
    }
}
