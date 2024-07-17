<?php

namespace Test\Request;
use \Exception;
use StarkInfra\Request;


class TestRequest
{
    public function get()
    {
        $path = "pix-request/";
        $query =  ["limit" => 10];
        $request = Request::get($path, $query)->content;
        $testAssertion = json_decode($request, true);

        if (!is_int($testAssertion["requests"][0]["amount"])) {
            throw new Exception("failed");
        }
    }

    public function postAndDelete()
    {
        $path = "issuing-holder";
        $ext = strval(random_int(1, 999999));
        $body =  [
            "holders" => [
                [
                    "name" => "Holder Test",
                    "taxId" => "012.345.678-90",
                    "externalId" => $ext,
                    "tags" => ["Traveler Employee"],
                ]
            ]
        ];
        $request = Request::post($path, $body)->content;
        $testAssertion = json_decode($request, true);
        if ($testAssertion["holders"][0]["externalId"] != $ext) {
            throw new Exception("failed");
        }

        $path = "issuing-holder/" . $testAssertion["holders"][0]["id"];
        $request = Request::delete($path)->content;
        $testAssertion = json_decode($request, true);
        if ($testAssertion["message"] != "Card Holder(s) successfully canceled") {
            throw new Exception("failed");
        }
    }

    public function patch()
    {
        $path = "issuing-holder";
        $query =  [ "limit" => 1, "status" => "active"];

        $request = Request::get($path, $query)->content;

        $testAssertion = json_decode($request, true);

        $path = "issuing-holder/" . $testAssertion["holders"][0]["id"];
        $request = Request::patch($path, ["tags" => ["arya", "stark"]])->content;
        $testAssertion = json_decode($request, true);

        if ($testAssertion["message"] != "Card Holder(s) successfully updated") {
            throw new Exception("failed");
        }
    }
}

echo "\n\Request:";

$test = new TestRequest();

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- postAndDelete";
$test->postAndDelete();
echo " - OK";

echo "\n\t- patch";
$test->patch();
echo " - OK";
