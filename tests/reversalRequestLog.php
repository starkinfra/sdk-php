<?php

namespace Test\ReversalRequestLog;

use \Exception;
use StarkInfra\ReversalRequest\Log;

class TestReversalRequestLog
{
    public function query()
    {
        $logs = Log::query(["limit" => 10]);

        foreach ($logs as $log) {
            if (is_null($log->id)) {
                throw new Exception("failed");
            }
        }
    }

    public function get()
    {
        $reversalRequest = iterator_to_array(Log::query(["limit" => 1]))[0];
        $reversalRequest = Log::get($reversalRequest->id);

        if (!is_string($reversalRequest->id)) {
            throw new Exception("failed");
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = Log::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $reversalRequestLog) {
                if (in_array($reversalRequestLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $reversalRequestLog->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 10) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nInfractionReportLog:";

$test = new TestReversalRequestLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";
