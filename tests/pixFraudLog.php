<?php

namespace Test\PixFraudLog;
use \Exception;
use \DateTime;
use StarkInfra\PixFraud;
use StarkInfra\PixFraud\Log;


class TestPixFraudLog
{
    public function queryAndGet()
    {
        $pixFraudLogs = iterator_to_array(Log::query(["limit" => 10]));

        if (count($pixFraudLogs) != 10) {
            throw new Exception("failed");
        }

        foreach ($pixFraudLogs as $log) {
            if (!is_string($log->type) | strlen($log->type) == 0) {
                throw new Exception("failed");
            }
            if (!($log->created instanceof DateTime)) {
                throw new Exception("failed");
            }
        }

        $pixFraudLog = Log::get($pixFraudLogs[0]->id);

        if ($pixFraudLogs[0]->id != $pixFraudLog->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i = 0; $i < 2; $i++) {
            list($page, $cursor) = Log::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $pixFraudLog) {
                if (in_array($pixFraudLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $pixFraudLog->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 10) {
            throw new Exception("failed");
        }
    }

    public function fraudIsParsed()
    {
        $pixFraudLog = iterator_to_array(Log::query(["limit" => 1]))[0];

        if (!($pixFraudLog->fraud instanceof PixFraud)) {
            throw new Exception("failed");
        }
        if (is_null($pixFraudLog->fraud->id)) {
            throw new Exception("failed");
        }
    }

    public function queryParams()
    {
        $pixFraudLogs = iterator_to_array(Log::query([
            "limit" => 10,
            "after" => "2020-04-01",
            "before" => "2020-04-30",
            "types" => ["created", "registered"],
            "fraudIds" => ["1", "2"],
            "ids" => ["1", "2"],
        ]));

        if (count($pixFraudLogs) != 0) {
            throw new Exception("failed");
        }
    }

    public function logResolvesUnderParent()
    {
        if (!class_exists("StarkInfra\\PixFraud\\Log")) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nPixFraudLog:";

$test = new TestPixFraudLog();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";

echo "\n\t- fraud is parsed";
$test->fraudIsParsed();
echo " - OK";

echo "\n\t- query params";
$test->queryParams();
echo " - OK";

echo "\n\t- log resolves under parent";
$test->logResolvesUnderParent();
echo " - OK";
