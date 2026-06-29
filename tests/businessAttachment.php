<?php

namespace Test\BusinessAttachment;
use \Exception;
use StarkInfra\BusinessAttachment;
use StarkInfra\BusinessIdentity;

class TestBusinessAttachment
{
    public function create()
    {
        $identity = BusinessIdentity::create([TestBusinessAttachment::exampleIdentity()])[0];
        if (is_null($identity->id)) {
            throw new Exception("failed");
        }

        $businessAttachment = BusinessAttachment::create([TestBusinessAttachment::exampleAttachment($identity->id)]);

        if (is_null($businessAttachment[0]->id)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $businessAttachments = iterator_to_array(BusinessAttachment::query(["limit" => 1]));

        if (count($businessAttachments) != 1) {
            throw new Exception("failed");
        }

        $businessAttachment = BusinessAttachment::get($businessAttachments[0]->id, ["expand" => ["content"]]);

        if ($businessAttachments[0]->id != $businessAttachment->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = BusinessAttachment::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $businessAttachment) {
                if (in_array($businessAttachment->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $businessAttachment->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 4) {
            throw new Exception("failed");
        }
    }

    public function exampleAttachment($id)
    {
        $params = [
            "name" => "articles-of-incorporation.png",
            "content" => base64_decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg=="),
            "contentType" => "image/png",
            "businessIdentityId" => $id,
        ];
        return new BusinessAttachment($params);
    }

    public function exampleIdentity()
    {
        $params = [
            "taxId" => "20.018.183/0001-80",
            "tags" => [
                'test',
                'testing'
            ],
        ];
        return new BusinessIdentity($params);
    }
}

echo "\n\nBusinessAttachment:";

$test = new TestBusinessAttachment();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
