<?php

namespace Test\PixClaim;

use \Exception;
use StarkInfra\PixClaim;
use StarkInfra\Error\InvalidSignatureError;

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
        print_r($claims);

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
        $claims = PixClaim::query(["status" => "sucess", "limit" => 1]);
        foreach ($claims as $claim) {
            if (is_null($claim->id)) {
                throw new Exception("failed");
            }
            if ($claim->status != "active") {
                throw new Exception("failed");
            }    
            $updatedPixClaim = PixClaim::update($claim->id, ["status" => "canceled"]);
            if ($updatedPixClaim->status != "failed") {
                throw new Exception("failed");
            }    
        }
    }

    const CONTENT = '{"receiverBranchCode": "0001", "cashierBankCode": "", "senderTaxId": "20.018.183/0001-80", "senderName": "Stark Bank S.A. - Instituicao de Pagamento", "id": "4508348862955520", "senderAccountType": "payment", "fee": 0, "receiverName": "Cora", "cashierType": "", "externalId": "", "method": "manual", "status": "processing", "updated": "2022-02-16T17:23:53.980250+00:00", "description": "", "tags": [], "receiverKeyId": "", "cashAmount": 0, "senderBankCode": "20018183", "senderBranchCode": "0001", "bankCode": "34052649", "senderAccountNumber": "5647143184367616", "receiverAccountNumber": "5692908409716736", "initiatorTaxId": "", "receiverTaxId": "34.052.649/0001-78", "created": "2022-02-16T17:23:53.980238+00:00", "flow": "in", "endToEndId": "E20018183202202161723Y4cqxlfLFcm", "amount": 1, "receiverAccountType": "checking", "reconciliationId": "", "receiverBankCode": "34052649"}';
    const VALID_SIGNATURE = "MEUCIQC7FVhXdripx/aXg5yNLxmNoZlehpyvX3QYDXJ8o02X2QIgVwKfJKuIS5RDq50NC/+55h/7VccDkV1vm8Q/7jNu0VM=";
    const INVALID_SIGNATURE = "MEUCIQDOpo1j+V40DNZK2URL2786UQK/8mDXon9ayEd8U0/l7AIgYXtIZJBTs8zCRR3vmted6Ehz/qfw1GRut/eYyvf1yOk=";

    public function parseRight()
    {
        $event_1 = PixClaim::parse(self::CONTENT, self::VALID_SIGNATURE);
        $event_2 = PixClaim::parse(self::CONTENT, self::VALID_SIGNATURE); // using cache

        if ($event_1 != $event_2) {
            throw new Exception("failed");
        }
    }

    public function parseWrong()
    {
        $error = false;
        try {
            $event = PixClaim::parse(self::CONTENT, self::INVALID_SIGNATURE);
        } catch (InvalidSignatureError $e) {
            $error = true;
        }

        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function parseMalformed()
    {
        $error = false;
        try {
            $event = PixClaim::parse(self::CONTENT, "something is definitely wrong");
        } catch (InvalidSignatureError $e) {
            $error = true;
        }

        if (!$error) {
            throw new Exception("failed");
        }
    }


    public static function example($schedule=false)
    {
        $params = [
            "accountCreated" => "2022-01-01",
            "accountNumber" => "76549", 
            "accountType" => "salary", 
            "branchCode" => "1234",
            "name"=> "Random Name",
            "taxId" => "012.345.678-90",
            "keyId" => "+551165857989",
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

echo "\n\t- parse right";
$test->parseRight();
echo " - OK";

echo "\n\t- parse wrong";
$test->parseWrong();
echo " - OK";

echo "\n\t- parse malformed";
$test->parseMalformed();
echo " - OK";
