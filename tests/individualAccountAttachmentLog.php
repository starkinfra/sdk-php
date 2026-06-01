<?php

namespace Test\IndividualAccountAttachmentLog;
use \Exception;
use StarkInfra\IndividualAccountAttachment;
use StarkInfra\IndividualAccountAttachment\Log;


class TestIndividualAccountAttachmentLog
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

    public function queryByAttachmentIds()
    {
        $attachments = iterator_to_array(IndividualAccountAttachment::query(["limit" => 2]));

        $attachmentIds = [];
        foreach ($attachments as $attachment) {
            array_push($attachmentIds, $attachment->id);
        }

        $logs = iterator_to_array(Log::query(["limit" => 5, "attachmentIds" => $attachmentIds]));

        foreach ($logs as $log) {
            if (gettype($log->attachment) == "string") {
                throw new Exception("failed");
            }
        }
    }

    public function logAttachmentIsParentType()
    {
        $logs = iterator_to_array(Log::query(["limit" => 1]));

        if (count($logs) == 0) {
            throw new Exception("failed");
        }

        $log = $logs[0];

        if (gettype($log->attachment) == "string") {
            throw new Exception("failed");
        }
    }
}

echo "\n\nIndividualAccountAttachmentLog:";

$test = new TestIndividualAccountAttachmentLog();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";

echo "\n\t- query by attachmentIds";
$test->queryByAttachmentIds();
echo " - OK";

echo "\n\t- log attachment is parent type";
$test->logAttachmentIsParentType();
echo " - OK";
