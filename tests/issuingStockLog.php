<?php

namespace Test\IssuingStockLog;
use \Exception;
use StarkInfra\IssuingStock\Log;


class TestIssuingStockLog
{
    public function query()
    {
        $logs = Log::query(["limit" => 5]);

        $count = 0;
        foreach ($logs as $log) {
            $count = $count + 1;
            if (is_null($log->id)) {
                throw new Exception("failed");
            }
        }
        if ($count != 5) {
            throw new Exception("failed");
        }    
    }

    public function get()
    {
        $issuingStock = iterator_to_array(Log::query(["limit" => 1]))[0];
        $issuingStock = Log::get($issuingStock->id);

        if (!is_string($issuingStock->id)) {
            throw new Exception("failed");
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = Log::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $issuingStockLog) {
                if (in_array($issuingStockLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $issuingStockLog->id);
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

echo "\n\nIssuingStockLog:";

$test = new TestIssuingStockLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";
