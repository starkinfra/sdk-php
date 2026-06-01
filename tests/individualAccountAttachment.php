<?php

namespace Test\IndividualAccountAttachment;
use \Exception;
use StarkInfra\IndividualAccountRequest;
use StarkInfra\IndividualAccountAttachment;
use StarkCore\Error\InputErrors;
use Test\IndividualAccountRequest\TestIndividualAccountRequest;


class TestIndividualAccountAttachment
{
    public function create()
    {
        $request = IndividualAccountRequest::create([TestIndividualAccountRequest::example()])[0];
        if (is_null($request->id)) {
            throw new Exception("failed");
        }

        $attachment = IndividualAccountAttachment::create([TestIndividualAccountAttachment::exampleSelfie($request->id)])[0];

        if (is_null($attachment->id)) {
            throw new Exception("failed");
        }
    }

    public function createEncodesDataUrl()
    {
        $raw = TestIndividualAccountAttachment::pngBytes();
        $contentType = "image/png";

        $attachment = new IndividualAccountAttachment([
            "type"             => "identity-front",
            "content"          => $raw,
            "contentType"      => $contentType,
            "accountRequestId" => "5189530608992256",
            "tags"             => ["test"],
        ]);

        $expected = "data:" . $contentType . ";base64," . base64_encode($raw);

        if (strpos($expected, "data:image/png;base64,") !== 0) {
            throw new Exception("failed");
        }

        $rebuilt = new IndividualAccountAttachment([
            "type"             => "identity-front",
            "content"          => $expected,
            "accountRequestId" => "5189530608992256",
        ]);

        if (strpos($rebuilt->content, "data:image/png;base64,") !== 0) {
            throw new Exception("failed");
        }
    }

    public function contentTypeNotOnResponse()
    {
        $request = IndividualAccountRequest::create([TestIndividualAccountRequest::example()])[0];
        if (is_null($request->id)) {
            throw new Exception("failed");
        }

        $attachment = IndividualAccountAttachment::create([TestIndividualAccountAttachment::exampleSelfie($request->id)])[0];

        $fetched = IndividualAccountAttachment::get($attachment->id);

        if (!is_null($fetched->contentType)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $attachments = iterator_to_array(IndividualAccountAttachment::query(["limit" => 1]));

        if (count($attachments) != 1) {
            throw new Exception("failed");
        }

        $attachment = IndividualAccountAttachment::get($attachments[0]->id);

        if ($attachments[0]->id != $attachment->id) {
            throw new Exception("failed");
        }
    }

    public function queryParams()
    {
        $attachments = iterator_to_array(IndividualAccountAttachment::query([
            "limit" => 10,
            "after" => "2020-04-01",
            "before" => "2020-04-30",
            "status" => "created",
            "tags" => ["iron", "suit"],
            "ids" => ["1", "2"],
        ]));

        if (count($attachments) != 0) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i = 0; $i < 2; $i++) {
            list($page, $cursor) = IndividualAccountAttachment::page($options = ["limit" => 1, "cursor" => $cursor]);
            foreach ($page as $attachment) {
                if (in_array($attachment->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $attachment->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) == 0) {
            throw new Exception("failed");
        }
    }

    public function createAndCancel()
    {
        $request = IndividualAccountRequest::create([TestIndividualAccountRequest::example()])[0];
        if (is_null($request->id)) {
            throw new Exception("failed");
        }

        $attachment = IndividualAccountAttachment::create([TestIndividualAccountAttachment::exampleSelfie($request->id)])[0];

        if (is_null($attachment->id)) {
            throw new Exception("failed");
        }

        $canceled = IndividualAccountAttachment::cancel($attachment->id);

        if (is_null($canceled->id) || $attachment->id != $canceled->id) {
            throw new Exception("failed");
        }

        if ($canceled->status != "deleted") {
            throw new Exception("failed");
        }
    }

    public function typeEnum()
    {
        $allowed = [
            "drivers-license-front",
            "drivers-license-back",
            "identity-front",
            "identity-back",
        ];

        $attachments = iterator_to_array(IndividualAccountAttachment::query(["limit" => 10]));

        foreach ($attachments as $attachment) {
            if (!is_null($attachment->type) && !in_array($attachment->type, $allowed)) {
                throw new Exception("failed");
            }
        }
    }

    public function sdkIdentifier()
    {
        if (!class_exists("StarkInfra\\IndividualAccountAttachment")) {
            throw new Exception("failed");
        }
        if (class_exists("StarkInfra\\AccountRequestAttachment")) {
            throw new Exception("failed");
        }
    }

    public function errorInvalidType()
    {
        $error = false;
        try {
            $request = IndividualAccountRequest::create([TestIndividualAccountRequest::example()])[0];
            $params = TestIndividualAccountAttachment::exampleParams($request->id);
            $params["type"] = "not-a-real-type";
            IndividualAccountAttachment::create([new IndividualAccountAttachment($params)]);
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function errorInvalidContent()
    {
        $error = false;
        try {
            $request = IndividualAccountRequest::create([TestIndividualAccountRequest::example()])[0];
            $params = TestIndividualAccountAttachment::exampleParams($request->id);
            $params["content"] = "";
            IndividualAccountAttachment::create([new IndividualAccountAttachment($params)]);
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function errorInvalidContentType()
    {
        $error = false;
        try {
            $request = IndividualAccountRequest::create([TestIndividualAccountRequest::example()])[0];
            $params = TestIndividualAccountAttachment::exampleParams($request->id);
            unset($params["contentType"]);
            IndividualAccountAttachment::create([new IndividualAccountAttachment($params)]);
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function errorNotFound()
    {
        $error = false;
        try {
            IndividualAccountAttachment::get("0");
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public static function pngBytes()
    {
        return "\x89PNG\r\n\x1a\n" . str_repeat("\x00\x01\x02\x03", 16);
    }

    public static function exampleParams($accountRequestId)
    {
        return [
            "type"             => "identity-front",
            "content"          => TestIndividualAccountAttachment::pngBytes(),
            "contentType"      => "image/png",
            "accountRequestId" => $accountRequestId,
            "tags"             => ["test"],
        ];
    }

    public static function exampleSelfie($accountRequestId)
    {
        return new IndividualAccountAttachment(TestIndividualAccountAttachment::exampleParams($accountRequestId));
    }
}

echo "\n\nIndividualAccountAttachment:";

$test = new TestIndividualAccountAttachment();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- create encodes data url";
$test->createEncodesDataUrl();
echo " - OK";

echo "\n\t- contentType not on response";
$test->contentTypeNotOnResponse();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- query params";
$test->queryParams();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";

echo "\n\t- create and cancel";
$test->createAndCancel();
echo " - OK";

echo "\n\t- type enum";
$test->typeEnum();
echo " - OK";

echo "\n\t- sdk identifier";
$test->sdkIdentifier();
echo " - OK";

echo "\n\t- error invalid type";
$test->errorInvalidType();
echo " - OK";

echo "\n\t- error invalid content";
$test->errorInvalidContent();
echo " - OK";

echo "\n\t- error invalid contentType";
$test->errorInvalidContentType();
echo " - OK";

echo "\n\t- error not found";
$test->errorNotFound();
echo " - OK";
