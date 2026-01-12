<?php

namespace Test\PixDisputeLog;
use \Exception;
use StarkInfra\PixDispute\Log;


class TestPixDisputeLog
{
    public function queryAndGet()
    {
        $PixDisputeLogs = iterator_to_array(Log::query(["limit" => 10, "types" => ["created"]]));

        if (count($PixDisputeLogs) != 10) {
            throw new Exception("failed");
        }

        foreach($PixDisputeLogs as $log) {
            if ($log->type != "created") {
                throw new Exception("failed");
            }
        }

        $PixDisputeLog = Log::get($PixDisputeLogs[0]->id);

        if ($PixDisputeLogs[0]->id != $PixDisputeLog->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = Log::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $PixDisputeLog) {
                if (in_array($PixDisputeLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $PixDisputeLog->id);
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

echo "\n\nPixDisputeLog:";

$test = new TestPixDisputeLog();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
