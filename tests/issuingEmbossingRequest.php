<?php

namespace Test\IssuingEmbossingRequest;
use \Exception;
use StarkInfra\IssuingCard;
use StarkInfra\IssuingEmbossingKit;
use StarkInfra\IssuingHolder;
use StarkInfra\IssuingProduct;
use StarkInfra\IssuingEmbossingRequest;


class TestIssuingEmbossingRequest
{
    public function create()
    {
        $request = IssuingEmbossingRequest::create([TestIssuingEmbossingRequest::exampleEmbossingRequest()])[0];
        if (is_null($request->id)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $requests = iterator_to_array(IssuingEmbossingRequest::query(["limit" => 1]));
        
        if (count($requests) != 1) {
            throw new Exception("failed");
        }

        $issuingEmbossingRequest = IssuingEmbossingRequest::get($requests[0]->id);

        if ($requests[0]->id != $issuingEmbossingRequest->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 1; $i++) { 
            list($page, $cursor) = IssuingEmbossingRequest::page($options = ["limit" => 1, "cursor" => $cursor]);
            foreach ($page as $issuingEmbossingRequest) {
                if (in_array($issuingEmbossingRequest->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $issuingEmbossingRequest->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 1) {
            throw new Exception("failed");
        }
    }

    public function exampleEmbossingRequest()
    {
        $productId = "";

        foreach (IssuingProduct::query() as $product) {
            if($product->holderType == "individual")
                $productId = $product->id;
        }

        $holder = IssuingHolder::create([
            new IssuingHolder([
                "name" => "Holder Test",
                "taxId" => "012.345.678-90",
                "externalId" => strval(random_int(1, 999999))
            ])
        ])[0];

        $cards = [];
        $cardToCreate = new IssuingCard([
            "holderName" => $holder->name,
            "holderTaxId" => $holder->taxId,
            "holderExternalId" => $holder->externalId,
            "type" => "physical",
            "productId" => $productId,
        ]);
        array_push($cards, $cardToCreate);

        $card = IssuingCard::create($cards)[0];

        $kitId = iterator_to_array(IssuingEmbossingKit::query(["limit" => 1]))[0]->id;

        $params = [
            "cardId" => $card->id, 
            "kitId" => $kitId,
            "displayName1" => "teste", 
            "shippingCity" => "Sao Paulo", 
            "shippingCountryCode" => "BRA", 
            "shippingDistrict" => "Bela Vista", 
            "shippingService" => "loggi", 
            "shippingStateCode" => "SP", 
            "shippingStreetLine1" => "teste", 
            "shippingStreetLine2" => "teste", 
            "shippingTrackingNumber" => "teste", 
            "shippingZipCode" => "12345-678",
            "embosserId" => "5746980898734080"
        ];
        return new IssuingEmbossingRequest($params);
    }
}

echo "\n\nIssuingEmbossingRequest:";

$test = new TestIssuingEmbossingRequest();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
