<?php

namespace Test\PixKeyLog;

use StarkInfra\PixKey\Log;
use Test\PixKey\TestPixKey;
use \Exception;

class TestPixKeyLog
{
    public function queryAndGet()
    {
        $pixKeyLogs = iterator_to_array(Log::query(["limit" => 10, "types" => ["created"]]));

        if (count($pixKeyLogs) != 10) {
            throw new Exception("failed");
        }

        foreach($pixKeyLogs as $log) {
            if ($log->type != "created") {
                throw new Exception("failed");
            }
        }

        $pixKeyLog = Log::get($pixKeyLogs[0]->id);

        if ($pixKeyLogs[0]->id != $pixKeyLog->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = Log::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $pixKeyLog) {
                if (in_array($pixKeyLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $pixKeyLog->id);
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

echo "\n\nPixKeyLog:";

$test = new TestPixKeyLog();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";