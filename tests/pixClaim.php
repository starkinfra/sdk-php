<?php

namespace Test\PixClaim;

use \Exception;
use StarkInfra\PixClaim;

class TestPixClaim
{
    public function create()
    {
        $claim = PixClaim::create(TestPixClaim::example());
        
        if (is_null($claim->id)){
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $claims = iterator_to_array(PixClaim::query(["limit" => 10]));

        if (count($claims) != 10){
            throw new Exception("failed");
        }

        $claim = PixClaim::get($claims[0]->id);

        if ($claims[0]->id != $claim->id){
            throw new Exception("failed");
        }
    }

    public function queryParams()
    {
        $claims = iterator_to_array(PixClaim::query([
            "limit" => 10,
            "after" => "2020-04-01",
            "before" => "2020-04-30",
            "status" => ['success','failed'],
            "ids" => ['1', '2'],
        ]));

        if (count($claims) != 0){
            throw new Exception("failed");
        }
    }

    public function queryIds()
    {
        $requests = iterator_to_array(PixClaim::query(["limit" => 10]));
        $requestsIdsExpected = array();
        for ($i = 0; $i < sizeof($requests); $i++) {
            array_push($requestsIdsExpected, $requests[$i]->id);
        }

        $requestsResult = iterator_to_array(PixClaim::query((["ids" => $requestsIdsExpected])));
        $requestsIdsResult = array();
        for ($i = 0; $i < sizeof($requestsResult); $i++) {
            array_push($requestsIdsResult, $requestsResult[$i]->id);
        }

        sort($requestsIdsExpected);
        sort($requestsIdsResult);

        if ($requestsIdsExpected != $requestsIdsResult) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i <2; $i++){
            list($page, $cursor) = PixClaim::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $pixClaim){
                if(in_array($pixClaim->id, $ids)){
                    throw new Exception("failed");
                }
                array_push($ids, $pixClaim->id);
            } 
            if ($cursor == null){
                break;
            }
        }
        if(count($ids) != 10){
            throw new Exception("failed");
        } 
    }

    public function update()
    {
        $claims = PixClaim::query(["status" => "delivered", "limit" => 1]);
        foreach ($claims as $claim) {
            if (is_null($claim->id)) {
                throw new Exception("failed");
            }
            if ($claim->status != "delivered") {
                throw new Exception("failed");
            }    
            $updatedPixClaim = PixClaim::update($claim->id, ["reason" => "userRequested","status" => "canceled"]);
            if (is_null($updatedPixClaim->id)) {
                throw new Exception("failed");
            } 
        }
    }

    public static function example($schedule=false)
    {
        $params = [
            "accountCreated" => "2022-01-01",
            "accountNumber" => "76549", 
            "accountType" => "salary", 
            "branchCode" => "1234",
            "name"=> "Tony Stark",
            "taxId" => "012.345.678-90",
            "keyId" => "+551195353399",
        ];
        return new PixClaim($params);
    }
}

echo "\n\nPixClaim:";

$test = new TestPixClaim();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- query";
$test->queryIds();
echo " - OK";

echo "\n\t- queryParams";
$test->queryParams();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";

echo "\n\t- update";
$test->update();
echo " - OK";
