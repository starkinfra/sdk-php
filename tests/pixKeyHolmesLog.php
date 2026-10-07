<?php

namespace Test\PixKeyHolmesLog;
use \Exception;
use StarkInfra\PixKeyHolmes\Log;


class TestPixKeyHolmesLog
{
    public function query()
    {
        $logs = iterator_to_array(Log::query(["limit" => 10]));

        if (count($logs) == 0) {
            throw new Exception("failed");
        }
        foreach ($logs as $log) {
            if (is_null($log->id) || is_null($log->type) || is_null($log->holmes->id)) {
                throw new Exception("failed");
            }
        }
    }

    public function get()
    {
        $logId = iterator_to_array(Log::query(["limit" => 1]))[0]->id;
        $log = Log::get($logId);

        if ($log->id != $logId || is_null($log->holmes->id)) {
            throw new Exception("failed");
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i = 0; $i < 2; $i++) {
            list($page, $cursor) = Log::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $holmesLog) {
                if (in_array($holmesLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $holmesLog->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 4) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nPixKeyHolmesLog:";

$test = new TestPixKeyHolmesLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";
