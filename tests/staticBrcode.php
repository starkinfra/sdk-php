<?php

namespace Test\StaticBrcode;
use \Exception;
use StarkInfra\StaticBrcode;
use StarkInfra\Utils\EndToEndId;
use StarkInfra\Error\InvalidSignatureError;


class TestStaticBrcode
{
    public function create()
    {
        $brcode = StaticBrcode::create([TestStaticBrcode::example()])[0];

        if (is_null($brcode->uuid)) {
            throw new Exception("failed");
        }
        if (is_null($brcode->cashierBankCode)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $brcodes = iterator_to_array(StaticBrcode::query(["limit" => 10]));

        if (count($brcodes) != 10) {
            throw new Exception("failed");
        }

        $brcode = StaticBrcode::get($brcodes[0]->uuid);

        if ($brcodes[0]->uuid != $brcode->uuid) {
            throw new Exception("failed");
        }
    }

    public function queryParams()
    {
        $brcodes = iterator_to_array(StaticBrcode::query([
            "fields" => ['amount', 'senderName'],
            "limit" => 10,
            "after" => "2020-04-01",
            "before" => "2020-04-30",
            "uuids" => ['1', '2'],
        ]));

        if (count($brcodes) != 0) {
            throw new Exception("failed");
        }
    }


    public function queryIds()
    {
        $brcodes = iterator_to_array(StaticBrcode::query(["limit" => 10]));
        $brcodesIdsExpected = array();
        for ($i = 0; $i < sizeof($brcodes); $i++) {
            array_push($brcodesIdsExpected, $brcodes[$i]->uuid);
        }

        $brcodesResult = iterator_to_array(StaticBrcode::query((["uuids" => $brcodesIdsExpected])));
        $brcodesIdsResult = array();
        for ($i = 0; $i < sizeof($brcodesResult); $i++) {
            array_push($brcodesIdsResult, $brcodesResult[$i]->uuid);
        }

        sort($brcodesIdsExpected);
        sort($brcodesIdsResult);

        if ($brcodesIdsExpected != $brcodesIdsResult) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $uuids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = StaticBrcode::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $brcode) {
                if (in_array($brcode->uuid, $uuids)) {
                    throw new Exception("failed");
                }
                array_push($uuids, $brcode->uuid);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($uuids) != 10) {
            throw new Exception("failed");
        }
    }

    public static function example()
    {
        $rand = rand(0, 2);
        $params = [
            "name" => ['Arya Stark', 'Jamie Lannister', 'Ned Stark'][$rand],
            "keyId" => ['AryaStark', 'JamieLannister', 'NedStark'][$rand] . "@hotmail.com" ,
            "city" => ['Sao Paulo', 'Rio de Janeiro'][rand(0, 1)],
            "amount" => [10000, 20000, 500000][rand(0, 2)],
            "cashierBankCode" => "20018183",
            "description" => "A StaticBrcode",
            "reconciliationId" => strval(mt_rand(0, 999999999999)),
        ];
        return new StaticBrcode($params);
    }
}

echo "\n\nStaticBrcode:";

$test = new TestStaticBrcode();

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
