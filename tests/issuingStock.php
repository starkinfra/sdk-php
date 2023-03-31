<?php

namespace Test\IssuingStock;
use \Exception;
use StarkInfra\IssuingStock;


class TestIssuingStock
{
    public function queryAndGet()
    {
        $issuingStocks = iterator_to_array(IssuingStock::query(["limit" => 1]));
        
        if (count($issuingStocks) != 1) {
            throw new Exception("failed");
        }

        $issuingStock = IssuingStock::get($issuingStocks[0]->id);

        if ($issuingStocks[0]->id != $issuingStock->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = IssuingStock::page($options = ["limit" => 1, "cursor" => $cursor]);
            foreach ($page as $issuingStock) {
                if (in_array($issuingStock->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $issuingStock->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 2) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nIssuingStock:";

$test = new TestIssuingStock();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
