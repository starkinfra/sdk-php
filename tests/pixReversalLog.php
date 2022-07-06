<?php

namespace Test\PixReversalLog;
use \Exception;
use StarkInfra\PixReversal\Log;


class TestPixReversalLog
{
    public function queryAndGet()
    {
        $pixReversalLogs = iterator_to_array(Log::query(["limit" => 10, "types" => ["processing"]]));

        if (count($pixReversalLogs) != 10) {
            throw new Exception("failed");
        }

        foreach($pixReversalLogs as $log) {
            if ($log->type != "processing") {
                throw new Exception("failed");
            }
        }

        $pixReversalLog = Log::get($pixReversalLogs[0]->id);

        if ($pixReversalLogs[0]->id != $pixReversalLog->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = Log::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $pixReversalLog) {
                if (in_array($pixReversalLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $pixReversalLog->id);
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

echo "\n\nPixReversalLog:";

$test = new TestPixReversalLog();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
