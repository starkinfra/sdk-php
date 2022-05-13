<?php

namespace Test\PixInfractionLog;

use \Exception;
use StarkInfra\PixInfraction\Log;

class TestPixInfractionLog
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
        $pixInfraction = iterator_to_array(Log::query(["limit" => 1]))[0];
        $pixInfraction = Log::get($pixInfraction->id);

        if (!is_string($pixInfraction->id)) {
            throw new Exception("failed");
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = Log::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $pixInfractionLog) {
                if (in_array($pixInfractionLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $pixInfractionLog->id);
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

echo "\n\nPixInfracionLog:";

$test = new TestPixInfractionLog();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";
