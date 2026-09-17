<?php

namespace Test\BusinessAccountRequest;
use \Exception;
use StarkInfra\BusinessAccountRequest;
use StarkInfra\BusinessAccountRequest\Owner;
use StarkInfra\BusinessAccountRequest\Address;
use StarkCore\Error\InputErrors;


class TestBusinessAccountRequest
{
    public function create()
    {
        $request = BusinessAccountRequest::create([TestBusinessAccountRequest::example()])[0];

        if (is_null($request->id)) {
            throw new Exception("failed");
        }
        if ($request->accountType != "business") {
            throw new Exception("failed");
        }
        if (!($request->address instanceof Address)) {
            throw new Exception("failed");
        }
        foreach ($request->owners as $owner) {
            if (!($owner instanceof Owner)) {
                throw new Exception("failed");
            }
        }
    }

    public function createFromArrays()
    {
        $request = BusinessAccountRequest::create([new BusinessAccountRequest(TestBusinessAccountRequest::exampleParams())])[0];

        if (is_null($request->id)) {
            throw new Exception("failed");
        }
        if (!($request->address instanceof Address)) {
            throw new Exception("failed");
        }
        if (!($request->owners[0] instanceof Owner)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $requests = iterator_to_array(BusinessAccountRequest::query(["limit" => 1]));

        if (count($requests) != 1) {
            throw new Exception("failed");
        }

        $request = BusinessAccountRequest::get($requests[0]->id);

        if ($requests[0]->id != $request->id) {
            throw new Exception("failed");
        }
        foreach ($request->owners as $owner) {
            if (!($owner instanceof Owner)) {
                throw new Exception("failed");
            }
        }
    }

    public function queryParams()
    {
        $requests = iterator_to_array(BusinessAccountRequest::query([
            "limit" => 10,
            "after" => "2020-04-01",
            "before" => "2020-04-30",
            "status" => "created",
            "tags" => ["iron", "suit"],
            "ids" => ["1", "2"],
        ]));

        if (count($requests) != 0) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i = 0; $i < 2; $i++) {
            list($page, $cursor) = BusinessAccountRequest::page($options = ["limit" => 1, "cursor" => $cursor]);
            foreach ($page as $request) {
                if (in_array($request->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $request->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) == 0) {
            throw new Exception("failed");
        }
    }

    public function statusEnum()
    {
        $allowed = ["created", "processing", "approved", "denied", "failed"];

        $requests = iterator_to_array(BusinessAccountRequest::query(["limit" => 10]));

        foreach ($requests as $request) {
            if (!is_null($request->status) && !in_array($request->status, $allowed)) {
                throw new Exception("failed");
            }
        }
    }

    public function ownersFromArraysAreParsed()
    {
        $request = new BusinessAccountRequest(TestBusinessAccountRequest::exampleParams());

        if (!($request->address instanceof Address)) {
            throw new Exception("failed");
        }
        if ($request->address->zipCode != "04538-132") {
            throw new Exception("failed");
        }
        if (count($request->owners) != 2) {
            throw new Exception("failed");
        }
        foreach ($request->owners as $owner) {
            if (!($owner instanceof Owner)) {
                throw new Exception("failed");
            }
            if (!is_null($owner->validatorLink)) {
                throw new Exception("failed");
            }
        }
    }

    public function errorNotFound()
    {
        $error = false;
        try {
            BusinessAccountRequest::get("0");
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public static function exampleParams()
    {
        return [
            "name"    => "Stark Bank " . strval(random_int(1, 999999)) . " S.A.",
            "taxId"   => "20.018.183/0001-80",
            "address" => [
                "street"       => "Av. Faria Lima",
                "number"       => "2000",
                "neighborhood" => "Itaim Bibi",
                "city"         => "Sao Paulo",
                "state"        => "SP",
                "zipCode"      => "04538-132",
                "complement"   => "Sala 42",
            ],
            "revenue" => 100000000,
            "owners"  => [
                [
                    "taxId" => "012.345.678-90",
                    "name"  => "Jamie Lannister",
                    "role"  => "partner",
                ],
                [
                    "taxId" => "812.531.960-36",
                    "name"  => "Cersei Lannister",
                    "role"  => "representative",
                ],
            ],
            "tags"    => ["employees", "monthly"],
        ];
    }

    public static function example()
    {
        $params = TestBusinessAccountRequest::exampleParams();
        $params["address"] = new Address($params["address"]);
        $params["owners"] = array_map(function ($owner) {
            return new Owner($owner);
        }, $params["owners"]);
        return new BusinessAccountRequest($params);
    }
}

echo "\n\nBusinessAccountRequest:";

$test = new TestBusinessAccountRequest();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- create from arrays";
$test->createFromArrays();
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

echo "\n\t- status enum";
$test->statusEnum();
echo " - OK";

echo "\n\t- owners from arrays are parsed";
$test->ownersFromArraysAreParsed();
echo " - OK";

echo "\n\t- error not found";
$test->errorNotFound();
echo " - OK";
