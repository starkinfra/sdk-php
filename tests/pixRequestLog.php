<?php

namespace Test\PixRequestLog;
use \Exception;
use StarkInfra\PixRequest\Log;


class TestPixRequestLog
{
    public function queryAndGet()
    {
        $pixRequestLogs = iterator_to_array(Log::query(["limit" => 10, "types" => ["created"]]));

        if (count($pixRequestLogs) != 10) {
            throw new Exception("failed");
        }

        foreach($pixRequestLogs as $log) {
            if ($log->type != "created") {
                throw new Exception("failed");
            }
        }

        $pixRequestLog = Log::get($pixRequestLogs[0]->id);

        if ($pixRequestLogs[0]->id != $pixRequestLog->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = Log::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $pixRequestLog) {
                if (in_array($pixRequestLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $pixRequestLog->id);
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

echo "\n\nPixRequestLog:";

$test = new TestPixRequestLog();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
