<?php

namespace Test\IssuingRestock;
use \Exception;
use StarkInfra\IssuingRestock;


class TestIssuingRestock
{
    public function create()
    {
        $issuingRestock = IssuingRestock::create([TestIssuingRestock::exampleRestock()])[0];
        if (is_null($issuingRestock->id)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $issuingRestocks = iterator_to_array(IssuingRestock::query(["limit" => 5]));
        
        if (count($issuingRestocks) != 5) {
            throw new Exception("failed");
        }

        $issuingRestock = IssuingRestock::get($issuingRestocks[0]->id);

        if ($issuingRestocks[0]->id != $issuingRestock->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = IssuingRestock::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $issuingRestock) {
                if (in_array($issuingRestock->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $issuingRestock->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 4) {
            throw new Exception("failed");
        }
    }

    public function exampleRestock()
    {
        $params = [
            "count" => 1000,
            "stockId" => "6526579068895232"
        ];
        return new IssuingRestock($params);
    }
}

echo "\n\nIssuingRestock:";

$test = new TestIssuingRestock();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
