<?php

namespace Test\IssuingProduct;
use \Exception;
use StarkInfra\IssuingProduct;


class TestIssuingProduct
{
    public function query()
    {
        $products = IssuingProduct::query();

        foreach ($products as $product) {
            if (is_null($product->id)) {
                throw new Exception("failed");
            }
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = IssuingProduct::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $product) {
                if (is_null($product->id) or in_array($product->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $product->id);
            }
            if ($cursor == null) {
                break;
            }
        }
    }
}

echo "\n\nIssuingProduct:";

$test = new TestIssuingProduct();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";
