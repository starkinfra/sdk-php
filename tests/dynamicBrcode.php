<?php

namespace Test\DynamicBrcode;
use \DateTime;
use \Exception;
use \DateInterval;
use \DateTimeZone;
use StarkInfra\DynamicBrcode;
use StarkInfra\Utils\EndToEndId;
use StarkInfra\Error\InvalidSignatureError;


class TestDynamicBrcode
{
    const UUID = "21f174ab942843eb90837a5c3135dfd6";
    const VALID_SIGNATURE = "MEYCIQC+Ks0M54DPLEbHIi0JrMiWbBFMRETe/U2vy3gTiid3rAIhANMmOaxT03nx2bsdo+vg6EMhWGzdphh90uBH9PY2gJdd";
    const INVALID_SIGNATURE = "MEYCIVC+Ks0M54DPLEbHIi0JrMiWbBFMRETe/U2vy3gTiid3rAIhANMmOaxT03nx2bsdo+vg6EMhWGzdphh90uBH9PY2gJdd";
        
    public function create()
    {
        $brcode = DynamicBrcode::create([TestDynamicBrcode::brcodeExample()])[0];

        if (is_null($brcode->uuid)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $brcodes = iterator_to_array(DynamicBrcode::query(["limit" => 10]));

        if (count($brcodes) != 10) {
            throw new Exception("failed");
        }

        $brcode = DynamicBrcode::get($brcodes[0]->uuid);

        if ($brcodes[0]->uuid != $brcode->uuid) {
            throw new Exception("failed");
        }
    }

    public function queryParams()
    {
        $brcodes = iterator_to_array(DynamicBrcode::query([
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
        $brcodes = iterator_to_array(DynamicBrcode::query(["limit" => 10]));
        $brcodesIdsExpected = array();
        for ($i = 0; $i < sizeof($brcodes); $i++) {
            array_push($brcodesIdsExpected, $brcodes[$i]->uuid);
        }

        $brcodesResult = iterator_to_array(DynamicBrcode::query((["uuids" => $brcodesIdsExpected])));
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
            list($page, $cursor) = DynamicBrcode::page($options = ["limit" => 5, "cursor" => $cursor]);
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

    public function createResponseInstant()
    {
        $brcodeJson = DynamicBrcode::responseInstant(TestDynamicBrcode::instantReadExample());
        if (gettype($brcodeJson) != "string") {
            throw new Exception("failed");
        }
        if (strlen($brcodeJson) == 0) {
            throw new Exception("failed");
        }
    }

    public function createResponseDue()
    {
        $brcodeJson = DynamicBrcode::responseInstant(TestDynamicBrcode::dueReadExample());
        if (gettype($brcodeJson) != "string") {
            throw new Exception("failed");
        }
        if (strlen($brcodeJson) == 0) {
            throw new Exception("failed");
        }
    }

    public function verifyValidSignature()
    {
        $uuid = DynamicBrcode::verify(self::UUID, self::VALID_SIGNATURE);
        if (self::UUID != $uuid) {
            throw new Exception("failed");
        }
    }

    public function verifyInvalidSignature()
    {
        $error = false;
        try {
            $uuid = DynamicBrcode::verify(self::UUID, self::INVALID_SIGNATURE);
        } catch (InvalidSignatureError $e) {
            $error = true;
        }

        if (!$error) {
            throw new Exception("failed");
        }
    }

    public static function brcodeExample()
    {
        return new DynamicBrcode([
            "name" => ['Arya Stark', 'Jamie Lannister', 'Ned Stark'][rand(0, 2)],
            "city" => ['Sao Paulo', 'Rio de Janeiro'][rand(0, 1)],
            "externalId" => strval(mt_rand(0, 99999999999999999)),
            "type" => ['instant', 'due'][rand(0, 1)],
        ]);
    }

    public static function dueReadExample()
    {
        return [
            "version" => rand(0, 5),
            "created" => date_sub(new DateTime("now", new DateTimeZone('Europe/London')), new DateInterval('P32D'))->format('Y-m-d H:i:s'),
            "due" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval('P32D')))->format('Y-m-d H:i:s'),
            "expiration" => rand(1,5)*3600*24*2,
            "keyId" => "testePhp@gmail.com",
            "status" => ['created', 'paid', 'voided'][rand(0, 2)],
            "reconciliationId" => strval(rand(999999, 99999999)),
            "nominalAmount" => strval(mt_rand(10, 999999)),
            "senderName" => ['Arya Stark', 'Jamie Lannister', 'Ned Stark'][rand(0, 2)],
            "senderTaxId" => "012.345.678-90",
            "receiverName" => ['Arya Stark', 'Jamie Lannister', 'Ned Stark'][rand(0, 2)],
            "receiverStreetLine" => "Av. Paulista, 2537",
            "receiverCity" => ['Sao Paulo', 'Rio de Janeiro'][rand(0, 1)],
            "receiverStateCode" => "SP",
            "receiverZipCode" => "01311-300",
            "receiverTaxId" => "20.018.183/0001-80",
            "fine" => rand(0, 1000),
            "interest" => rand(0, 1000),
            "discounts" => rand(0, 1000),
            "description" => "teste Php"
        ];
    }

    public static function instantReadExample()
    {
        return [
            "version" => rand(0, 5),
            "created" => date_sub(new DateTime("now", new DateTimeZone('Europe/London')), new DateInterval('P32D'))->format('Y-m-d H:i:s'),
            "keyId" => "testePhp@gmail.com",
            "status" => ['created', 'paid', 'voided'][rand(0, 2)],
            "reconciliationId" => strval(rand(999999, 99999999)),
            "nominalAmount" => strval(mt_rand(10, 999999)),
        ];
    }
}

echo "\n\nDynamicBrcode:";

$test = new TestDynamicBrcode();

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

echo "\n\t- create response instant Brcode";
$test->createResponseInstant();
echo " - OK";

echo "\n\t- create response due Brcode";
$test->createResponseDue();
echo " - OK";

echo "\n\t- verify valid signature";
$test->verifyValidSignature();
echo " - OK";

echo "\n\t- verify invalid signature";
$test->verifyInvalidSignature();
echo " - OK";
