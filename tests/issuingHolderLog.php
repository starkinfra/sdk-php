<?php

namespace Test\IssuingHolderLog;

use \Exception;
use StarkInfra\IssuingHolder\Log;


class TestIssuingHolderLog
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
        $log = iterator_to_array(Log::query(["limit" => 1]))[0];
        $log = Log::get($log->id);

        if (!is_string($log->id)) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nIssuingHolderLog:";

$test = new TestIssuingHolderLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";
