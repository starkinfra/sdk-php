<?php

namespace Test\PixRequest;
use \Exception;
use StarkInfra\Error\InvalidSignatureError;
use StarkInfra\PixRequest;
use StarkInfra\Utils\EndToEndId;

class TestPixRequest
{
    public function create()
    {
        $request = PixRequest::create([TestPixRequest::example()])[0];

        if (is_null($request->id)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $requests = iterator_to_array(PixRequest::query(["limit" => 10]));

        if (count($requests) != 10) {
            throw new Exception("failed");
        }

        $request = PixRequest::get($requests[0]->id);

        if ($requests[0]->id != $request->id) {
            throw new Exception("failed");
        }
    }

    public function queryParams()
    {
        $requests = iterator_to_array(PixRequest::query([
            "fields" => ['amount', 'senderName'],
            "limit" => 10,
            "after" => "2020-04-01",
            "before" => "2020-04-30",
            "status" => ['success','failed'],
            "tags" => ['iron', 'suit'],
            "ids" => ['1', '2'],
            "externalIds"=> ['1','2'],
            "endToEndIds" => ['1', '2'],
        ]));
        print_r($requests);

        if (count($requests) != 0) {
            throw new Exception("failed");
        }
    }


    public function queryIds()
    {
        $requests = iterator_to_array(PixRequest::query(["limit" => 10]));
        $requestsIdsExpected = array();
        for ($i = 0; $i < sizeof($requests); $i++) {
            array_push($requestsIdsExpected, $requests[$i]->id);
        }

        $requestsResult = iterator_to_array(PixRequest::query((["ids" => $requestsIdsExpected])));
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
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = PixRequest::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $pixRequest) {
                if (in_array($pixRequest->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $pixRequest->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 10) {
            throw new Exception("failed");
        }
    }


    const CONTENT = '{"receiverBranchCode": "0001", "cashierBankCode": "", "senderTaxId": "20.018.183/0001-80", "senderName": "Stark Bank S.A. - Instituicao de Pagamento", "id": "4508348862955520", "senderAccountType": "payment", "fee": 0, "receiverName": "Cora", "cashierType": "", "externalId": "", "method": "manual", "status": "processing", "updated": "2022-02-16T17:23:53.980250+00:00", "description": "", "tags": [], "receiverKeyId": "", "cashAmount": 0, "senderBankCode": "20018183", "senderBranchCode": "0001", "bankCode": "34052649", "senderAccountNumber": "5647143184367616", "receiverAccountNumber": "5692908409716736", "initiatorTaxId": "", "receiverTaxId": "34.052.649/0001-78", "created": "2022-02-16T17:23:53.980238+00:00", "flow": "in", "endToEndId": "E20018183202202161723Y4cqxlfLFcm", "amount": 1, "receiverAccountType": "checking", "reconciliationId": "", "receiverBankCode": "34052649"}';
    const VALID_SIGNATURE = "MEUCIQC7FVhXdripx/aXg5yNLxmNoZlehpyvX3QYDXJ8o02X2QIgVwKfJKuIS5RDq50NC/+55h/7VccDkV1vm8Q/7jNu0VM=";
    const INVALID_SIGNATURE = "MEUCIQDOpo1j+V40DNZK2URL2786UQK/8mDXon9ayEd8U0/l7AIgYXtIZJBTs8zCRR3vmted6Ehz/qfw1GRut/eYyvf1yOk=";

    public function parseRight()
    {
        $event_1 = PixRequest::parse(self::CONTENT, self::VALID_SIGNATURE);
        $event_2 = PixRequest::parse(self::CONTENT, self::VALID_SIGNATURE); // using cache

        if ($event_1 != $event_2) {
            throw new Exception("failed");
        }
    }

    public function parseWrong()
    {
        $error = false;
        try {
            $event = PixRequest::parse(self::CONTENT, self::INVALID_SIGNATURE);
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
            $event = PixRequest::parse(self::CONTENT, "something is definitely wrong");
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
            "amount" => 10,
            "externalId" => "php-" . $uuid = mt_rand(0, 0xffffffff),
            "senderAccountNumber" => "00000-0",
            "senderBranchCode" => "0000",
            "senderAccountType" => "checking",
            "senderName" => "Joao",
            "senderTaxId" => "01234567890",
            "receiverBankCode" => "00000000",
            "receiverAccountNumber" => "00000-1",
            "receiverBranchCode" => "0001",
            "receiverAccountType" => "checking",
            "receiverName" => "maria",
            "receiverTaxId" => "01234567890",
            "endToEndId" => EndToEndId::create("34052649"),
        ];
        return new PixRequest($params);
    }
}

echo "\n\nPixRequest:";

$test = new TestPixRequest();

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

echo "\n\t- parse right";
$test->parseRight();
echo " - OK";

echo "\n\t- parse wrong";
$test->parseWrong();
echo " - OK";

echo "\n\t- parse malformed";
$test->parseMalformed();
echo " - OK";

