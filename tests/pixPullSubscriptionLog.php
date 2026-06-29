<?php

namespace Test\PixPullSubscriptionLog;
use \Exception;
use StarkInfra\PixPullSubscription;


class TestPixPullSubscriptionLog
{
    public function query()
    {
        $logs = iterator_to_array(PixPullSubscription\Log::query(["limit" => 10]));
        if (count($logs) > 10) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i = 0; $i < 2; $i++) {
            list($page, $cursor) = PixPullSubscription\Log::page(["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $log) {
                if (in_array($log->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $log->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) > 10) {
            throw new Exception("failed");
        }
    }

    public function queryParams()
    {
        $logs = iterator_to_array(PixPullSubscription\Log::query([
            "limit" => 5,
            "after" => "2026-01-01",
            "before" => "2026-04-30",
            "types" => ["failed"],
            "subscriptionIds" => ["1", "2"],
        ]));
        if (count($logs) != 0) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nPixPullSubscriptionLog:";

$test = new TestPixPullSubscriptionLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";

echo "\n\t- queryParams";
$test->queryParams();
echo " - OK";
