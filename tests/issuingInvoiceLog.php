<?php

namespace Test\IssuingInvoiceLog;

use \Exception;
use StarkInfra\IssuingInvoice\Log;


class TestIssuingInvoiceLog
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
            list($page, $cursor) = Log::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $invoiceLog) {
                if (in_array($invoiceLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $invoiceLog->id);
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

echo "\n\nIssuingInvoiceLog:";

$test = new TestIssuingInvoiceLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";
