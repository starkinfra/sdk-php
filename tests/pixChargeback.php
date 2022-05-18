<?php

namespace Test\PixChargeback;

use Exception;
use StarkInfra\PixChargeback;
use StarkInfra\Utils\ReturnId;

class TestPixChargeback
{
    public function create()
    {
        $chargebacks = PixChargeback::create([TestPixChargeback::example()])[0];

        if (is_null($chargebacks->id)) {
            throw new Exception("failed");
        }
    }

    public function queryAndCancel()
    {
        $chargebacks = iterator_to_array(PixChargeback::query(["limit"=>10, "status"=>"delivered"]));

        if (count($chargebacks) != 10){
            throw new Exception("failed");
        }
        $chargeback = PixChargeback::cancel($chargebacks[0]->id);
        if ($chargebacks[0]->status == "canceled") {
            throw new Exception("failed");
        } 
        if ($chargebacks[0]->id != $chargeback->id) {
            throw new Exception("failed");
        } 
    } 
    
    public function queryAndGet()
    {
        $chargebacks = iterator_to_array(PixChargeback::query(["limit" => 10]));

        if (count($chargebacks) != 10) {
            throw new Exception("failed");
        }
        $chargeback = PixChargeback::get($chargebacks[0]->id);
        
        if ($chargebacks[0]->id != $chargeback->id) {
            throw new Exception("failed");
        }
    }

    public function queryIds()
    {
        $chargebacks = iterator_to_array(PixChargeback::query(["limit" => 10]));
        $chargebacksIdsExpected = array();
        for ($i = 0; $i < sizeof($chargebacks); $i++) {
            array_push($chargebacksIdsExpected, $chargebacks[$i]->id);
        }

        $chargebacksResult = iterator_to_array(PixChargeback::query((["ids" => $chargebacksIdsExpected])));
        $chargebacksIdsResult = array();
        for ($i = 0; $i < sizeof($chargebacksResult); $i++) {
            array_push($chargebacksIdsResult, $chargebacksResult[$i]->id);
        }

        sort($chargebacksIdsExpected);
        sort($chargebacksIdsResult);

        if ($chargebacksIdsExpected != $chargebacksIdsResult) {
            throw new Exception("failed");
        }
    }

    public function page(){

        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = PixChargeback::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $chargebackRequest) {
                if (in_array($chargebackRequest->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $chargebackRequest->id);
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
        $chargebacks = PixChargeback::query(["status" => "delivered", "limit" => 1]);
        foreach ($chargebacks as $chargeback) {
            if (is_null($chargeback->id)) {
                throw new Exception("failed");
            }
            if ($chargeback->status != "delivered") {
                throw new Exception("failed");
            }    
            $updatedChargeback = PixChargeback::update($chargeback->id, ["result" => "accepted", "reversalReferenceId" => ReturnId::create($chargeback->senderBankCode)]);
            if ($updatedChargeback->result != "accepted") {
                throw new Exception("failed");
            }    
        }
    }

    public static function example()
    {
        $params = [
            "amount" => 100,
            "referenceId" => "E35547753202205101724tM25SPNVfSp",
            "reason" => "fraud",
        ];
        return new PixChargeback($params);
    }
}

echo "\nPixChargeback:";

$test = new TestPixChargeback();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query and cancel";
$test->queryAndCancel();
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
