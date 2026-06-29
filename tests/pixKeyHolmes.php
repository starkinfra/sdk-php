<?php

namespace Test\PixKeyHolmes;
use \Exception;
use StarkInfra\PixKeyHolmes;


class TestPixKeyHolmes
{
    public function create()
    {
        $pixKeyHolmes = PixKeyHolmes::create([TestPixKeyHolmes::exampleHolmes()])[0];

        if (is_null($pixKeyHolmes->id)) {
            throw new Exception("failed");
        }

        if (is_null($pixKeyHolmes->status) || $pixKeyHolmes->status === "") {
            throw new Exception("failed");
        }

        if (is_null($pixKeyHolmes->created)) {
            throw new Exception("failed");
        }
        if (is_null($pixKeyHolmes->updated)) {
            throw new Exception("failed");
        }
    }

    public function query()
    {
        $pixKeyHolmes = iterator_to_array(PixKeyHolmes::query(["limit" => 1]));

        if (count($pixKeyHolmes) != 1) {
            throw new Exception("failed");
        }

        if (is_null($pixKeyHolmes[0]->id)) {
            throw new Exception("failed");
        }
    }

    public function queryParams()
    {
        $pixKeyHolmes = iterator_to_array(PixKeyHolmes::query([
            "limit" => 10,
            "after" => "2020-04-01",
            "before" => "2020-04-30",
            "status" => ["solved", "solving"],
            "tags" => ["iron", "suit"],
            "ids" => ["1", "2"],
        ]));

        if (count($pixKeyHolmes) != 0) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i = 0; $i < 2; $i++) {
            list($page, $cursor) = PixKeyHolmes::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $pixKeyHolmes) {
                if (in_array($pixKeyHolmes->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $pixKeyHolmes->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 4) {
            throw new Exception("failed");
        }
    }

    public static function exampleHolmes()
    {
        $params = [
            "keyId" => "valid@sandbox.com",
            "tags" => [
                "War supply",
                "Invoice #1234",
            ],
        ];
        return new PixKeyHolmes($params);
    }
}

echo "\n\nPixKeyHolmes:";

$test = new TestPixKeyHolmes();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- query params";
$test->queryParams();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
