<?php

namespace Test\IssuingWithdrawal;

use \Exception;
use StarkInfra\IssuingWithdrawal;


class TestIssuingWithdrawal
{

    public function query()
    {
        $withdrawals = IssuingWithdrawal::query(["limit" => 10]);

        foreach ($withdrawals as $withdrawal) {
            if (is_null($withdrawal->id)) {
                throw new Exception("failed");
            }
        }
    }

    public function get()
    {
        $withdrawal = iterator_to_array(IssuingWithdrawal::query(["limit" => 1]))[0];
        $withdrawal = IssuingWithdrawal::get($withdrawal->id);

        if (!is_string($withdrawal->id)) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nIssuingWithdrawal:";

$test = new TestIssuingWithdrawal();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";
