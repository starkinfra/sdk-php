<?php

namespace Test\ReversalRequest;

use Exception;
use StarkInfra\ReversalRequest;
use StarkInfra\Utils\EndToEndId;


class TestReversalRequest
{
    public function createAndDelete()
    {
        $reversals = ReversalRequest::create([TestReversalRequest::example()])[0];
        if ($reversals[0]->securityCode == "***") {
            throw new Exception("failed");
        }
        $reversalId = $reversals[0]->id;
       
        $reversal = ReversalRequest::delete($reversalId);
        if ($reversal->status != "canceled") {
            throw new Exception("failed");
        }
    }

    public function queryAndDelete()
    {
        $reversals = iterator_to_array(ReversalRequest::query(["limit"=>10]));

        if (count($reversals) != 10){
            throw new Exception("failed");
        }

        $reversal = ReversalRequest::delete($reversals[0]->id);

        if ($reversals[0]->id != $reversal->id) {
            throw new Exception("failed");
        } 
    } 
    
    public function queryAndGet()
    {
        $reversals = iterator_to_array(ReversalRequest::query(["limit" => 10]));

        if (count($reversals) != 10) {
            throw new Exception("failed");
        }

        $reversal = ReversalRequest::get($reversals[0]->id);

        if ($reversals[0]->id != $reversal->id) {
            throw new Exception("failed");
        }
    }

    public function queryIds()
    {
        $reversals = iterator_to_array(ReversalRequest::query(["limit" => 10]));
        $reversalsIdsExpected = array();
        for ($i = 0; $i < sizeof($reversals); $i++) {
            array_push($reversalsIdsExpected, $reversals[$i]->id);
        }

        $reversalsResult = iterator_to_array(ReversalRequest::query((["ids" => $reversalsIdsExpected])));
        $reversalsIdsResult = array();
        for ($i = 0; $i < sizeof($reversalsResult); $i++) {
            array_push($reversalsIdsResult, $reversalsResult[$i]->id);
        }

        sort($reversalsIdsExpected);
        sort($reversalsIdsResult);

        if ($reversalsIdsExpected != $reversalsIdsResult) {
            throw new Exception("failed");
        }
    }

    public function page(){

        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = ReversalRequest::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $reversalRequest) {
                if (in_array($reversalRequest->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $reversalRequest->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 10) {
            throw new Exception("failed");
        }
    }

    public function update()
    {
        $reversals = ReversalRequest::query(["status" => "active", "limit" => 1]);
        foreach ($reversals as $reversal) {
            if (is_null($reversal->id)) {
                throw new Exception("failed");
            }
            if ($reversal->status != "active") {
                throw new Exception("failed");
            }    
            $updatedReversal = ReversalRequest::update($reversal->id, ["status" => "blocked"]);
            if ($updatedReversal->status != "blocked") {
                throw new Exception("failed");
            }    
        }
    }

    public static function example()
    {
        $params = [
            "amount"=> null,
            "referenceId"=> null,
            "reason" => "flaw",
        ];
        return new ReversalRequest($params);
    }
}

echo "\nReversalRequest:";

$test = new TestReversalRequest();

echo "\n\t- create and delete";
$test->createAndDelete();
echo " - OK";

echo "\n\t- query and delete";
$test->queryAndDelete();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- query Ids";
$test->queryIds();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";

echo "\n\t- update";
$test->update();
echo " - OK";


