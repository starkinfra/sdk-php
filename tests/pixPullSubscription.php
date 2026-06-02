<?php

namespace Test\PixPullSubscription;
use \Exception;
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

    const CONTENT = '{"bacenId": "RR20170329000000000000000003", "externalId": "test-external-id", "id": "5656565656565656", "installmentStart": "2026-04-01T12:00:00+00:00", "interval": "month", "receiverName": "Edward Stark", "receiverTaxId": "20.018.183/0001-80", "senderAccountNumber": "876543-2", "senderBankCode": "20018183", "senderBranchCode": "1357-9", "senderTaxId": "01234567890", "type": "push", "status": "active", "flow": "out", "amount": 11234, "due": "", "installmentEnd": "", "created": "2026-04-01T12:00:00+00:00", "updated": "2026-04-01T12:00:00+00:00"}';
    const INVALID_SIGNATURE = "MEUCIQDOpo1j+V40DNZK2URL2786UQK/8mDXon9ayEd8U0/l7AIgYXtIZJBTs8zCRR3vmted6Ehz/qfw1GRut/eYyvf1yOk=";

    public function parseWrong()
    {
        $error = false;
        try {
            PixPullSubscription::parse(self::CONTENT, self::INVALID_SIGNATURE);
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
            PixPullSubscription::parse(self::CONTENT, "something is definitely wrong");
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
        $bacenId = "RR" . "32160637" . $now->format("YmdHi") . sprintf("%07d", mt_rand(0, 9999999));
        $params = [
            "bacenId" => $bacenId,
            "externalId" => "php-" . mt_rand(0, 0xffffffff),
            "installmentStart" => $now->format("Y-m-d\TH:i:s+00:00"),
            "interval" => "month",
            "receiverName" => "Stark Bank",
            "receiverTaxId" => "39.908.427/0001-28",
            "receiverBankCode" => "32160637",
            "senderAccountNumber" => "876543-2",
            "senderBankCode" => "32160637",
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

echo "\n\t- parse wrong";
$test->parseWrong();
echo " - OK";

echo "\n\t- parse malformed";
$test->parseMalformed();
echo " - OK";
