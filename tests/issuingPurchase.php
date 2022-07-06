<?php

namespace Test\IssuingPurchase;
use \Exception;
use StarkInfra\IssuingPurchase;
use StarkInfra\Error\InvalidSignatureError;


class TestIssuingPurchase
{
    const CONTENT = '{"acquirerId": "236090", "amount": 100, "cardId": "5671893688385536", "cardTags": [], "endToEndId": "2fa7ef9f-b889-4bae-ac02-16749c04a3b6", "holderId": "5917814565109760", "holderTags": [], "isPartialAllowed": false, "issuerAmount": 100, "issuerCurrencyCode": "BRL", "merchantAmount": 100, "merchantCategoryCode": "bookStores", "merchantCountryCode": "BRA", "merchantCurrencyCode": "BRL", "merchantFee": 0, "merchantId": "204933612653639", "merchantName": "COMPANY 123", "methodCode": "token", "purpose": "purchase", "score": null, "tax": 0, "walletId": ""}';
    const VALID_SIGNATURE = "MEUCIBxymWEpit50lDqFKFHYOgyyqvE5kiHERi0ZM6cJpcvmAiEA2wwIkxcsuexh9BjcyAbZxprpRUyjcZJ2vBAjdd7o28Q=";
    const INVALID_SIGNATURE = "MEUCIQDOpo1j+V40DNZK2URL2786UQK/8mDXon9ayEd8U0/l7AIgYXtIZJBTs8zCRR3vmted6Ehz/qfw1GRut/eYyvf1yOk=";

    public function queryAndGet()
    {
        $purchases = IssuingPurchase::query(["limit" => 10]);

        foreach ($purchases as $purchase) {
            if (is_null($purchase->id)) {
                throw new Exception("failed");
            }

            $purchase = iterator_to_array(IssuingPurchase::query(["limit" => 1]))[0];
            $purchase = IssuingPurchase::get($purchase->id);

            if (!is_string($purchase->id)) {
                throw new Exception("failed");
            }
        }        
    }

    public function parseRight()
    {
        $authorization_1 = IssuingPurchase::parse(self::CONTENT, self::VALID_SIGNATURE);
        $authorization_2 = IssuingPurchase::parse(self::CONTENT, self::VALID_SIGNATURE); // using cache

        if ($authorization_1 != $authorization_2) {
            throw new Exception("failed");
        }
    }

    public function parseWrong()
    {
        $error = false;
        try {
            $authorization = IssuingPurchase::parse(self::CONTENT, self::INVALID_SIGNATURE);
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
            $authorization = IssuingPurchase::parse(self::CONTENT, "something is definitely wrong");
        } catch (InvalidSignatureError $e) {
            $error = true;
        }

        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function createResponse()
    {
        $response = IssuingPurchase::response(["status"=>"accepted", "amount"=>1000]);
        if (gettype($response) != "string") {
            throw new Exception("failed");
        }
        if (strlen($response) == 0) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nIssuingPurchase:";

$test = new TestIssuingPurchase();

echo "\n\t- query and get";
$test->queryAndGet();
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
