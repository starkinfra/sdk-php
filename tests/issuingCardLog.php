<?php

namespace Test\IssuingCardLog;
use \Exception;
use StarkInfra\IssuingCard\Log;


class TestIssuingCardLog
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
        $card = iterator_to_array(Log::query(["limit" => 1]))[0];
        $card = Log::get($card->id);

        if (!is_string($card->id)) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nIssuingCardLog:";

$test = new TestIssuingCardLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";
