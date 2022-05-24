<?php

namespace Test\CreditNoteLog;

use \Exception;
use StarkInfra\CreditNote\Log;


class TestCreditNoteLog
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
        $creditNote = iterator_to_array(Log::query(["limit" => 1]))[0];
        $creditNote = Log::get($creditNote->id);

        if (!is_string($creditNote->id)) {
            throw new Exception("failed");
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = Log::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $creditNoteLog) {
                if (in_array($creditNoteLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $creditNoteLog->id);
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

echo "\n\nCreditNoteLog:";

$test = new TestCreditNoteLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";
