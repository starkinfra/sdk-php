<?php

namespace Test\PixPullSubscription;
use \Exception;
use StarkInfra\Event;
use StarkInfra\PixPullSubscription;
use StarkCore\Error\InvalidSignatureError;


class TestPixPullSubscription
{
    public function create()
    {
        $subscription = PixPullSubscription::create([TestPixPullSubscription::example()])[0];

        if (is_null($subscription->id)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $subscriptions = iterator_to_array(PixPullSubscription::query(["limit" => 10]));

        if (count($subscriptions) > 10) {
            throw new Exception("failed");
        }

        if (count($subscriptions) > 0) {
            $subscription = PixPullSubscription::get($subscriptions[0]->id);
            if ($subscriptions[0]->id != $subscription->id) {
                throw new Exception("failed");
            }
        }
    }

    public function queryParams()
    {
        $subscriptions = iterator_to_array(PixPullSubscription::query([
            "limit" => 10,
            "after" => "2026-01-01",
            "before" => "2026-04-30",
            "status" => ["active"],
            "tags" => ["test"],
            "ids" => ["1", "2"],
        ]));

        if (count($subscriptions) != 0) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i = 0; $i < 2; $i++) {
            list($page, $cursor) = PixPullSubscription::page(["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $subscription) {
                if (in_array($subscription->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $subscription->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) > 10) {
            throw new Exception("failed");
        }
    }

    const CONTENT = '{"event": {"created": "2026-03-17T20:24:02.006080+00:00", "id": "5739991880695808", "log": {"created": "2026-03-17T20:23:58.050406+00:00", "errors": [], "id": "5340798381981696", "reason": "", "subscription": {"amount": 52064, "amountMinLimit": 0, "bacenId": "RR321606372026170317231564231", "created": "2026-03-17T20:23:57.255567+00:00", "description": "A Lannister always pays his debts", "due": "2026-04-17T02:59:59.999000+00:00", "externalId": "606512134", "flow": "out", "id": "5656970050666496", "installmentEnd": "", "installmentStart": "2026-03-18T02:59:59.999999+00:00", "interval": "month", "pullRetryLimit": 3, "receiverBankCode": "32160637", "receiverName": "Stark Bank", "receiverTaxId": "39.908.427/0001-28", "referenceCode": "36135971", "senderAccountNumber": "55213", "senderBankCode": null, "senderBranchCode": "356", "senderCityCode": "", "senderFinalName": "STARK SCD S.A.", "senderFinalTaxId": "39.908.427/0001-28", "senderTaxId": "99.999.919/9999-79", "status": "created", "tags": [], "type": "push", "updated": "2026-03-17T20:23:58.050421+00:00"}, "type": "delivering"}, "subscription": "pix-pull-subscription", "workspaceId": "4828094443552768"}}';
    const VALID_SIGNATURE = "MEUCIQCCZWR4+JYoDNENLnRbSCGGZf+atOaG4q8jWB3ADgc+DQIgIZ1LuXLZ06pke2qzaMNTlDLwcriuH+S3ve1aTQeqNK0=";
    const INVALID_SIGNATURE = "MEUCIQCCZWR4+JYoDNENLnRbSCGGZf+atOaG4q8jWB3ADgc+DQIgIZ1LuXLZ06pke2qzaMNTlDLwcriuH+S3ve1aTQEqNK0=";

    public function parseRight()
    {
        $event_1 = Event::parse(self::CONTENT, self::VALID_SIGNATURE);
        $event_2 = Event::parse(self::CONTENT, self::VALID_SIGNATURE);

        if ($event_1 != $event_2) {
            throw new Exception("failed");
        }
        if ($event_1->id == null) {
            throw new Exception("failed");
        }
    }

    public function parseWrong()
    {
        $error = false;
        try {
            Event::parse(self::CONTENT, self::INVALID_SIGNATURE);
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
            Event::parse(self::CONTENT, "something is definitely wrong");
        } catch (InvalidSignatureError $e) {
            $error = true;
        }

        if (!$error) {
            throw new Exception("failed");
        }
    }

    public static function example()
    {
        $now = new \DateTime("now", new \DateTimeZone("UTC"));
        $bankCode = $_SERVER["SANDBOX_BANK_CODE"];
        $bacenId = "RR" . \StarkInfra\Utils\BacenId::create($bankCode);
        $params = [
            "bacenId" => $bacenId,
            "externalId" => "php-" . mt_rand(0, 0xffffffff),
            "installmentStart" => $now->format("Y-m-d\TH:i:s+00:00"),
            "interval" => "month",
            "receiverName" => "Stark Bank",
            "receiverTaxId" => "39.908.427/0001-28",
            "receiverBankCode" => $bankCode,
            "senderAccountNumber" => "876543-2",
            "senderBankCode" => $bankCode,
            "senderBranchCode" => "1357-9",
            "senderTaxId" => "01234567890",
            "senderFinalName" => "STARK SCD S.A.",
            "senderFinalTaxId" => "39908427000128",
            "type" => "push",
            "amount" => 52064,
            "referenceCode" => "ref-php-" . mt_rand(0, 0xffffffff),
            "pullRetryLimit" => 3,
            "description" => "A Lannister always pays his debts",
            "tags" => ["test", "pix-pull"],
        ];
        return new PixPullSubscription($params);
    }
}

echo "\n\nPixPullSubscription:";

$test = new TestPixPullSubscription();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
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
