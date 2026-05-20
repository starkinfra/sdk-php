<?php

namespace Test\PixPullRequest;
use \Exception;
use StarkInfra\PixPullRequest;


class TestPixPullRequest
{
    public function create()
    {
        $request = PixPullRequest::create([TestPixPullRequest::example()])[0];
        if (is_null($request->id)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $requests = iterator_to_array(PixPullRequest::query(["limit" => 10]));
        if (count($requests) > 10) {
            throw new Exception("failed");
        }
        if (count($requests) > 0) {
            $request = PixPullRequest::get($requests[0]->id);
            if ($requests[0]->id != $request->id) {
                throw new Exception("failed");
            }
        }
    }

    public function queryParams()
    {
        $requests = iterator_to_array(PixPullRequest::query([
            "limit" => 10,
            "after" => "2026-01-01",
            "before" => "2026-04-30",
            "status" => ["created"],
            "tags" => ["test"],
            "ids" => ["1", "2"],
            "subscriptionIds" => ["1", "2"],
        ]));
        if (count($requests) != 0) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i = 0; $i < 2; $i++) {
            list($page, $cursor) = PixPullRequest::page(["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $request) {
                if (in_array($request->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $request->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) > 10) {
            throw new Exception("failed");
        }
    }

    public static function getActiveSubscriptionId()
    {
        $subscriptions = iterator_to_array(\StarkInfra\PixPullSubscription::query(["limit" => 10, "status" => ["active"]]));
        if (count($subscriptions) > 0) {
            return $subscriptions[0]->id;
        }
        // Fall back: create one and use its id (it may not be active immediately, but best effort)
        $created = \StarkInfra\PixPullSubscription::create([\Test\PixPullSubscription\TestPixPullSubscription::example()]);
        return $created[0]->id;
    }

    public static function example()
    {
        $now = new \DateTime("now", new \DateTimeZone("UTC"));
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $random = "";
        for ($i = 0; $i < 11; $i++) {
            $random .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        $endToEndId = "E" . "32160637" . $now->format("YmdHi") . $random;
        $due = new \DateTime("+2 days", new \DateTimeZone("UTC"));
        return new PixPullRequest([
            "amount" => 52064,
            "due" => $due->format("Y-m-d\TH:i:s+00:00"),
            "endToEndId" => $endToEndId,
            "receiverAccountNumber" => "876543-2",
            "receiverAccountType" => "checking",
            "receiverBankCode" => "32160637",
            "reconciliationId" => "recon-" . mt_rand(0, 0xffffffff),
            "subscriptionId" => self::getActiveSubscriptionId(),
            "attemptType" => "default",
            "tags" => ["test", "pix-pull"],
        ]);
    }
}

echo "\n\nPixPullRequest:";

$test = new TestPixPullRequest();

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
