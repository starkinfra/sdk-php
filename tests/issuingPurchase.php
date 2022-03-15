<?php

namespace Test\IssuingPurchase;

use \Exception;
use StarkInfra\IssuingPurchase;


class TestIssuingPurchase
{

    public function query()
    {
        $purchases = IssuingPurchase::query(["limit" => 10]);

        foreach ($purchases as $purchase) {
            if (is_null($purchase->id)) {
                throw new Exception("failed");
            }
        }
    }

    public function get()
    {
        $purchase = iterator_to_array(IssuingPurchase::query(["limit" => 1]))[0];
        $purchase = IssuingPurchase::get($purchase->id);

        if (!is_string($purchase->id)) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nIssuingPurchase:";

$test = new TestIssuingPurchase();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";
