<?php

namespace Test\LedgerLog;
use \Exception;
use StarkInfra\Ledger\Log;


class TestLedgerLog
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

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = Log::page(["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $log) {
                if (is_null($log->id) or in_array($log->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $log->id);
            }
            if ($cursor == null) {
                break;
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

echo "\n\nLedgerLog:";

$test = new TestLedgerLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";
