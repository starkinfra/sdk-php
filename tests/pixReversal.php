<?php

namespace Test\PixReversal;
use \Exception;
use StarkInfra\PixRequest;
use StarkInfra\PixReversal;
use StarkInfra\Error\InvalidSignatureError;


class TestPixReversal
{
    public function create()
    {
        $reversal = PixReversal::create([TestPixReversal::example()])[0];

        if (is_null($reversal->id)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $reversals = iterator_to_array(PixReversal::query(["limit" => 10]));

        if (count($reversals) != 10) {
            throw new Exception("failed");
        }

        $reversal = PixReversal::get($reversals[0]->id);

        if ($reversals[0]->id != $reversal->id) {
            throw new Exception("failed");
        }
    }

    public function queryIds()
    {
        $reversals = iterator_to_array(PixReversal::query(["limit" => 10]));
        $reversalsIdsExpected = array();
        for ($i = 0; $i < sizeof($reversals); $i++) {
            array_push($reversalsIdsExpected, $reversals[$i]->id);
        }

        $reversalsResult = iterator_to_array(PixReversal::query((["ids" => $reversalsIdsExpected])));
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

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = PixReversal::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $pixReversal) {
                if (in_array($pixReversal->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $pixReversal->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 10) {
            throw new Exception("failed");
        }
    }

    const CONTENT = '{"event": {"created": "2022-02-15T20:45:09.852878+00:00", "id": "5015597159022592", "log": {"created": "2022-02-15T20:45:09.436621+00:00", "errors": [{"code": "insufficientFunds", "message": "Amount of funds available is not sufficient to cover the specified transfer"}], "id": "5288053467774976", "reversal": {"amount": 1000, "bankCode": "34052649", "cashAmount": 0, "cashierBankCode": "", "cashierType": "", "created": "2022-02-15T20:45:08.210009+00:00", "description": "For saving my life", "endToEndId": "E34052649202201272111u34srod1a91", "externalId": "141322efdgber1ecd1s342341321", "fee": 0, "flow": "out", "id": "5137269514043392", "initiatorTaxId": "", "method": "manual", "receiverAccountNumber": "000001", "receiverAccountType": "checking", "receiverBankCode": "00000001", "receiverBranchCode": "0001", "receiverKeyId": "", "receiverName": "Jamie Lennister", "receiverTaxId": "45.987.245/0001-92", "reconciliationId": "", "senderAccountNumber": "000000", "senderAccountType": "checking", "senderBankCode": "34052649", "senderBranchCode": "0000", "senderName": "tyrion Lennister", "senderTaxId": "012.345.678-90", "status": "failed", "tags": [], "updated": "2022-02-15T20:45:09.436661+00:00"}, "type": "failed"}, "subscription": "pix-reversal.out", "workspaceId": "5692908409716736"}}';
    const VALID_SIGNATURE = "";
    const INVALID_SIGNATURE = "MEYCIQD0oFxFQX0fI6B7oqjwLhkRhkDjrOiD86wjjEKWdzkJbgIhAPNGUUdlNpYBe+npOaHa9WJopzy3WJYl8XJG6f4ek2R/";

    public function parseRight()
    {
        $event_1 = PixReversal::parse(self::CONTENT, self::VALID_SIGNATURE);
        $event_2 = PixReversal::parse(self::CONTENT, self::VALID_SIGNATURE); // using cache

        if ($event_1 != $event_2) {
            throw new Exception("failed");
        }
    }

    public function parseWrong()
    {
        $error = false;
        try {
            $event = PixReversal::parse(self::CONTENT, self::INVALID_SIGNATURE);
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
            $event = PixReversal::parse(self::CONTENT, "something is definitely wrong");
        } catch (InvalidSignatureError $e) {
            $error = true;
        }

        if (!$error) {
            throw new Exception("failed");
        }
    }

        public function createResponse()
    {
        $params = [
            "status" => "approved",
        ];

        $response = PixReversal::response($params);

        if (gettype($response) != "string") {
            throw new Exception("failed");
        }
        if (strlen($response) == 0) {
            throw new Exception("failed");
        }
    }

    public static function example($schedule=false)
    {
        $params = [
            "amount" => 1,
            "externalId" => "php-" . $uuid = mt_rand(0, 0xffffffff),
            "endToEndId" =>  self::getEndToEndId(),
            "reason" => "fraud",
            "tags" => [
                "lannister",
                "chargeback"
            ],
        ];
        return new PixReversal($params);
    }

    private static function getEndToEndId()
    {
        $cursor = null;
        $endToEndId = null;
        while ($endToEndId == null){
            list($page, $cursor) = PixRequest::page($options = ["limit" => 100, "status" => "success", "cursor" => $cursor]);
            for ($i = 1; $i <= 2; $i++){
                if ($page[$i]->amount > 1 && $page[$i]->flow == "in") {
                    $endToEndId = $page[$i]->endToEndId;
                    break;
                }
            }
            if ($cursor == null) {
                break;
            }
        }
        if ($endToEndId == null) {
            throw new Exception("There are no inbound PixRequests to reverse");
        }
        return $endToEndId;
    }
}

echo "\n\nPixReversal:";

$test = new TestPixReversal();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- query";
$test->queryIds();
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

echo "\n\t- create response";
$test->createResponse();
echo " - OK";
