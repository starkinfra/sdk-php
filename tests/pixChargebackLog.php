<?php

namespace Test\PixChargebackLog;

use \Exception;
use StarkInfra\PixChargeback\Log;

class TestPixChargebackLog
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
        $chargebackRequest = iterator_to_array(Log::query(["limit" => 1]))[0];
        $chargebackRequest = Log::get($chargebackRequest->id);

        if (!is_string($chargebackRequest->id)) {
            throw new Exception("failed");
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = Log::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $chargebackRequestLog) {
                if (in_array($chargebackRequestLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $chargebackRequestLog->id);
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

echo "\n\nPixchargeback:";

$test = new TestPixChargebackLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";
