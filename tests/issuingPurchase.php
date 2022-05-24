<?php

namespace Test\IssuingPurchase;

use \Exception;
use StarkInfra\IssuingPurchase;


class TestIssuingPurchase
{

    public function queryAndGet()
    {
        $purchases = IssuingPurchase::query(["limit" => 10]);

        foreach ($purchases as $purchase) {
            if (is_null($purchase->id)) {
                throw new Exception("failed");
            }

            $purchase = iterator_to_array(IssuingPurchase::query(["limit" => 1]))[0];
            $purchase = IssuingPurchase::get($purchase->id);

            if (!is_string($purchase->id)) {
                throw new Exception("failed");
            }
        }        
    }
}

echo "\n\nIssuingPurchase:";

$test = new TestIssuingPurchase();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";
