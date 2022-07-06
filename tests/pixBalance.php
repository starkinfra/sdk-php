<?php

namespace Test\PixBalance;
use \Exception;
use StarkInfra\PixBalance;


class TestPixBalance
{
    public function get()
    {
        $balance = PixBalance::get();
        if (!is_int($balance->amount)) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nPixBalance:";

$test = new TestPixBalance();

echo "\n\t- get";
$test->get();
echo " - OK";
