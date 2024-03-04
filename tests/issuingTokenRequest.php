<?php

namespace Test\IssuingTokenRequest;
use \Exception;
use StarkCore\Utils\Request;
use StarkInfra\IssuingTokenRequest;
use StarkInfra\IssuingCard;


class TestIssuingTokenRequest
{
    public function create()
    {
        $request = TestIssuingTokenRequest::exampleRequest();
        $issuingTokenRequest = IssuingTokenRequest::create($request);
        if (is_null($issuingTokenRequest->content)) {
            throw new Exception("failed");
        }
    }

    public function exampleRequest()
    {
        $card = iterator_to_array(IssuingCard::query(["limit" => 1, "expand" => ["rules"]]))[0];
        $params = [
            "cardId" => $card->id,
            "walletId" => "google",
            "methodCode" => "app"
        ];
        return new IssuingTokenRequest($params);
    }
}

echo "\n\nIssuingTokenRequest:";

$test = new TestIssuingTokenRequest();

echo "\n\t- create";
$test->create();
echo " - OK";
