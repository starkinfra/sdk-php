<?php

namespace Test\IndividualDocumentLog;
use \Exception;
use StarkInfra\IndividualDocument\Log;


class TestIndividualDocumentLog
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
        $individualDocument = iterator_to_array(Log::query(["limit" => 1]))[0];
        $individualDocument = Log::get($individualDocument->id);

        if (!is_string($individualDocument->id)) {
            throw new Exception("failed");
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = Log::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $individualDocumentLog) {
                if (in_array($individualDocumentLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $individualDocumentLog->id);
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

echo "\n\nIndividualDocumentLog:";

$test = new TestIndividualDocumentLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";
