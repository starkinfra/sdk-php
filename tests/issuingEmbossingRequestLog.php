<?php

namespace Test\IssuingEmbossingRequestLog;
use \Exception;
use StarkInfra\IssuingEmbossingRequest\Log;


class TestIssuingEmbossingRequestLog
{
    public function query()
    {
        $logs = Log::query(["limit" => 1]);

        $count = 0;
        foreach ($logs as $log) {
            $count = $count + 1;
            if (is_null($log->id)) {
                throw new Exception("failed");
            }
        }
        if ($count != 1) {
            throw new Exception("failed");
        }    
    }

    public function get()
    {
        $logs = iterator_to_array(Log::query(["limit" => 1]))[0];
        $log = Log::get($logs->id);

        if (!is_string($log->id)) {
            throw new Exception("failed");
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = Log::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $log) {
                if (in_array($log->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $log->id);
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

echo "\n\nIssuingEmbossingRequestLog:";

$test = new TestIssuingEmbossingRequestLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";
