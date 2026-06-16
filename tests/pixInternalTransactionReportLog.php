<?php

namespace Test\PixInternalTransactionReportLog;
use \Exception;
use StarkInfra\PixInternalTransactionReport\Log;


class TestPixInternalTransactionReportLog
{
    public function queryAndGet()
    {
        $reportLogs = iterator_to_array(Log::query(["limit" => 10]));

        if (count($reportLogs) != 10) {
            throw new Exception("failed");
        }

        $reportLog = Log::get($reportLogs[0]->id);

        if ($reportLogs[0]->id != $reportLog->id) {
            throw new Exception("failed");
        }

        if (is_null($reportLog->report) | is_null($reportLog->report->id)) {
            throw new Exception("failed");
        }
    }

    public function queryParams()
    {
        $reportLogs = iterator_to_array(Log::query([
            "limit" => 10,
            "after" => "2020-04-01",
            "before" => "2020-04-30",
            "types" => ["success", "failed"],
            "reportIds" => ['1', '2'],
        ]));

        if (count($reportLogs) != 0) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i = 0; $i < 2; $i++) {
            list($page, $cursor) = Log::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $reportLog) {
                if (in_array($reportLog->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $reportLog->id);
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

echo "\n\nPixInternalTransactionReportLog:";

$test = new TestPixInternalTransactionReportLog();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- queryParams";
$test->queryParams();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
