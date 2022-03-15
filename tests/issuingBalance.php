<?php

namespace Test\IssuingBalance;

use \Exception;
use StarkInfra\IssuingBalance;


class TestIssuingBalance
{

    public function get()
    {
        $balance = IssuingBalance::get();

        if (is_null($balance->id)) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nIssuingBalance:";

$test = new TestIssuingBalance();

echo "\n\t- get";
$test->get();
echo " - OK";
