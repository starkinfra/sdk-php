<?php

namespace Test\PixClaimLog;
use \Exception;
use StarkInfra\PixClaim\Log;

class TestPixClaimLog
{
    public function queryAndGet()
    {
        $pixClaimLogs = iterator_to_array(Log::query(["limit" => 10, "types" => ["created"]]));

        if (count($pixClaimLogs) != 10) {
            throw new Exception("failed");
        }

        foreach($pixClaimLogs as $log) {
            if ($log->type != "created") {
                throw new Exception("failed");
            }
        }

        $pixClaimLog = Log::get($pixClaimLogs[0]->id);

        if ($pixClaimLogs[0]->id != $pixClaimLog->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = Log::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $pixClaimLog) {
                if (in_array($pixClaimLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $pixClaimLog->id);
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

echo "\n\nPixClaimLog:";

$test = new TestPixClaimLog();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
