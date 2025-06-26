<?php

namespace Test\Utils;
use StarkInfra\DynamicBrcode;


class UtilsDynamicBrcode
{
    public static function createDynamicBrcodeByType($type)
    {
        return DynamicBrcode::create([
            new DynamicBrcode([
                "name" => ['Arya Stark', 'Jamie Lannister', 'Ned Stark'][rand(0, 2)],
                "city" => ['Sao Paulo', 'Rio de Janeiro'][rand(0, 1)],
                "externalId" => strval(mt_rand(0, 99999999999999999)),
                "type" => $type
            ]
        )])[0];
    }
}