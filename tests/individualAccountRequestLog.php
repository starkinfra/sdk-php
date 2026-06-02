<?php

namespace Test\IndividualAccountRequestLog;
use \Exception;
use StarkInfra\IndividualAccountRequest;
use StarkInfra\IndividualAccountRequest\Log;


class TestIndividualAccountRequestLog
{
    public function queryAndGet()
    {
        $logs = iterator_to_array(Log::query(["limit" => 1]));

        if (count($logs) != 1) {
            throw new Exception("failed");
        }

        foreach ($logs as $log) {
            if (is_null($log->id)) {
                throw new Exception("failed");
            }
        }

        $log = Log::get($logs[0]->id);

        if ($logs[0]->id != $log->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i = 0; $i < 2; $i++) {
            list($page, $cursor) = Log::page($options = ["limit" => 1, "cursor" => $cursor]);
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
        if (count($ids) == 0) {
            throw new Exception("failed");
        }
    }

    public function queryByAccountRequestIds()
    {
        $requests = iterator_to_array(IndividualAccountRequest::query(["limit" => 2]));

        $requestIds = [];
        foreach ($requests as $request) {
            array_push($requestIds, $request->id);
        }

        $logs = iterator_to_array(Log::query(["limit" => 5, "accountRequestIds" => $requestIds]));

        foreach ($logs as $log) {
            if (gettype($log->request) == "string") {
                throw new Exception("failed");
            }
        }
    }

    public function logRequestIsParentType()
    {
        $logs = iterator_to_array(Log::query(["limit" => 1]));

        if (count($logs) == 0) {
            throw new Exception("failed");
        }

        $log = $logs[0];

        if (gettype($log->request) == "string") {
            throw new Exception("failed");
        }
    }
}

echo "\n\nIndividualAccountRequestLog:";

$test = new TestIndividualAccountRequestLog();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";

echo "\n\t- query by accountRequestIds";
$test->queryByAccountRequestIds();
echo " - OK";

echo "\n\t- log request is parent type";
$test->logRequestIsParentType();
echo " - OK";
