<?php

namespace Test\IssuingCard;
use \Exception;
use Test\Utils\Rule;
use StarkInfra\IssuingCard;
use StarkInfra\IssuingHolder;
use StarkInfra\IssuingProduct;

class TestIssuingCard
{
    public function query()
    {
        $cards = IssuingCard::query(["limit" => 10, "expand" => ["rules"]]);
        foreach ($cards as $card) {
            if (is_null($card->id)) {
                throw new Exception("failed");
            }
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = IssuingCard::page(["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $card) {
                if (is_null($card->id) or in_array($card->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $card->id);
            }
            if ($cursor == null) {
                break;
            }
        }
    }

    public function get()
    {
        $card = iterator_to_array(IssuingCard::query(["limit" => 1, "expand" => ["rules"]]))[0];
        $card = IssuingCard::get($card->id);

        if (!is_string($card->id)) {
            throw new Exception("failed");
        }
    }

    public function postAndCancel()
    {
        $cards = IssuingCard::create(
            TestIssuingCard::generateExampleCardsJson(2), 
            ["expand" => ["rules", "securityCode"]]
        );
        if ($cards[0]->securityCode == "***") {
            throw new Exception("failed");
        }
        $cardId = $cards[0]->id;
        $card = IssuingCard::update($cardId, ["displayName" => "Updated Name"]);
        if ($card->displayName != "Updated Name") {
            throw new Exception("failed");
        }
        $card = IssuingCard::cancel($cardId);
        if ($card->status != "canceled") {
            throw new Exception("failed");
        }
    }

    public function update()
    {
        $cards = IssuingCard::query(["status" => "active", "limit" => 1]);
        foreach ($cards as $card) {
            if (is_null($card->id)) {
                throw new Exception("failed");
            }
            if ($card->status != "active") {
                throw new Exception("failed");
            }    
            $updatedCard = IssuingCard::update($card->id, ["status" => "blocked"]);
            if ($updatedCard->status != "blocked") {
                throw new Exception("failed");
            }    
        }
    }

    public static function generateExampleCardsJson($n=1)
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
                "externalId" => strval(random_int(1, 999999)),
                "tags" => ["Traveler Employee"],
                "rules" => Rule::generateExampleRulesJson()
            ])
        ])[0];

        $cards = [];
        foreach (range(1, $n) as $index) {
            $card = new IssuingCard([
                "holderName" => $holder->name,
                "holderTaxId" => $holder->taxId,
                "holderExternalId" => $holder->externalId,
                "productId" => $productId,
                "rules" => Rule::generateExampleRulesJson()
            ]);
            array_push($cards, $card);
        }
        return $cards;
    }
}

echo "\n\nIssuingCard:";

$test = new TestIssuingCard();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- post and cancel";
$test->postAndCancel();
echo " - OK";

echo "\n\t- update";
$test->update();
echo " - OK";
