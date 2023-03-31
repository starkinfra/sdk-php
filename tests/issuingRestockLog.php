<?php

namespace Test\IssuingRestockLog;
use \Exception;
use StarkInfra\IssuingRestock\Log;


class TestIssuingRestockLog
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
        $issuingRestock = iterator_to_array(Log::query(["limit" => 1]))[0];
        $issuingRestock = Log::get($issuingRestock->id);

        if (!is_string($issuingRestock->id)) {
            throw new Exception("failed");
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = Log::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $issuingRestockLog) {
                if (in_array($issuingRestockLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $issuingRestockLog->id);
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

echo "\n\nIssuingRestockLog:";

$test = new TestIssuingRestockLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";
