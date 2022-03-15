<?php

namespace Test\IssuingPurchaseLog;

use \Exception;
use StarkInfra\IssuingPurchase\Log;


class TestIssuingPurchaseLog
{

    public function query()
    {
        $purchases = Log::query(["limit" => 10]);

        foreach ($purchases as $purchase) {
            if (is_null($purchase->id)) {
                throw new Exception("failed");
            }
        }
    }

    public function get()
    {
        $log = iterator_to_array(Log::query(["limit" => 1]))[0];
        $log = Log::get($log->id);

        if (!is_string($log->id)) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nIssuingPurchaseLog:";

$test = new TestIssuingPurchaseLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";
