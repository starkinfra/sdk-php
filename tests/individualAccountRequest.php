<?php

namespace Test\IndividualAccountRequest;
use \Exception;
use StarkInfra\IndividualAccountRequest;
use StarkCore\Error\InputErrors;


class TestIndividualAccountRequest
{
    public function create()
    {
        $request = IndividualAccountRequest::create([TestIndividualAccountRequest::example()])[0];

        if (is_null($request->id)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $requests = iterator_to_array(IndividualAccountRequest::query(["limit" => 1]));

        if (count($requests) != 1) {
            throw new Exception("failed");
        }

        $request = IndividualAccountRequest::get($requests[0]->id);

        if ($requests[0]->id != $request->id) {
            throw new Exception("failed");
        }
    }

    public function queryParams()
    {
        $requests = iterator_to_array(IndividualAccountRequest::query([
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
            list($page, $cursor) = IndividualAccountRequest::page($options = ["limit" => 1, "cursor" => $cursor]);
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

    public function queryGetAndUpdate()
    {
        $request = IndividualAccountRequest::create([TestIndividualAccountRequest::example()])[0];

        if (is_null($request->id)) {
            throw new Exception("failed");
        }

        $updated = IndividualAccountRequest::update($request->id, ["name" => "Updated Name"]);

        if ($updated->name != "Updated Name") {
            throw new Exception("failed");
        }
    }

    public function updateReplacesAddress()
    {
        $request = IndividualAccountRequest::create([TestIndividualAccountRequest::example()])[0];

        if (is_null($request->id)) {
            throw new Exception("failed");
        }

        $newAddress = [
            "street"       => "Avenida Paulista",
            "number"       => "1000",
            "neighborhood" => "Bela Vista",
            "city"         => "Sao Paulo",
            "state"        => "SP",
            "zipCode"      => "01310100",
        ];

        $updated = IndividualAccountRequest::update($request->id, ["address" => $newAddress]);

        if (is_null($updated->id)) {
            throw new Exception("failed");
        }
    }

    public function statusEnum()
    {
        $allowed = ["created", "processing", "success", "failed", "canceled"];

        $requests = iterator_to_array(IndividualAccountRequest::query(["limit" => 10]));

        foreach ($requests as $request) {
            if (!is_null($request->status) && !in_array($request->status, $allowed)) {
                throw new Exception("failed");
            }
        }
    }

    public function outputOnlyFieldsRoundTrip()
    {
        $request = new IndividualAccountRequest([
            "name"        => "Tony Stark",
            "taxId"       => "012.345.678-90",
            "address"     => [
                "street"       => "Rua do Estilo Barroco",
                "number"       => "648",
                "neighborhood" => "Santo Amaro",
                "city"         => "Sao Paulo",
                "state"        => "SP",
                "zipCode"      => "05724005",
            ],
            "income"      => 1000000,
            "tags"        => ["test"],
            "id"          => "5189530608992256",
            "status"      => "processing",
            "accountType" => "individual",
            "flags"       => [],
            "created"     => "2026-05-26T12:34:56.000000+00:00",
            "updated"     => "2026-05-26T12:34:56.000000+00:00",
        ]);

        if ($request->id != "5189530608992256") {
            throw new Exception("failed");
        }
        if ($request->status != "processing") {
            throw new Exception("failed");
        }
        if ($request->accountType != "individual") {
            throw new Exception("failed");
        }
    }

    public function addressIsNestedObject()
    {
        $request = TestIndividualAccountRequest::example();

        if (!is_array($request->address) && !is_object($request->address)) {
            throw new Exception("failed");
        }

        $address = (array) $request->address;

        if (!isset($address["street"]) || !isset($address["number"]) || !isset($address["neighborhood"])) {
            throw new Exception("failed");
        }
        if (!isset($address["city"]) || !isset($address["state"]) || !isset($address["zipCode"])) {
            throw new Exception("failed");
        }
    }

    public function errorInvalidName()
    {
        $error = false;
        try {
            $params = TestIndividualAccountRequest::exampleParams();
            $params["name"] = "";
            IndividualAccountRequest::create([new IndividualAccountRequest($params)]);
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function errorInvalidTaxId()
    {
        $error = false;
        try {
            $params = TestIndividualAccountRequest::exampleParams();
            $params["taxId"] = "000.000.000-00";
            IndividualAccountRequest::create([new IndividualAccountRequest($params)]);
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function errorInvalidAddress()
    {
        $error = false;
        try {
            $params = TestIndividualAccountRequest::exampleParams();
            $params["address"] = [
                "street" => "Rua do Estilo Barroco",
            ];
            IndividualAccountRequest::create([new IndividualAccountRequest($params)]);
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function errorInvalidIncome()
    {
        $error = false;
        try {
            $params = TestIndividualAccountRequest::exampleParams();
            $params["income"] = -1;
            IndividualAccountRequest::create([new IndividualAccountRequest($params)]);
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function errorInvalidStatus()
    {
        $error = false;
        try {
            $request = IndividualAccountRequest::create([TestIndividualAccountRequest::example()])[0];
            IndividualAccountRequest::update($request->id, ["status" => "not-a-real-status"]);
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
            IndividualAccountRequest::get("0");
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
            "name"    => "Tony Stark " . strval(random_int(1, 999999)),
            "taxId"   => "012.345.678-90",
            "address" => [
                "street"       => "Rua do Estilo Barroco",
                "number"       => "648",
                "neighborhood" => "Santo Amaro",
                "city"         => "Sao Paulo",
                "state"        => "SP",
                "zipCode"      => "05724005",
            ],
            "income"  => 1000000,
            "tags"    => ["employees", "monthly"],
        ];
    }

    public static function example()
    {
        return new IndividualAccountRequest(TestIndividualAccountRequest::exampleParams());
    }
}

echo "\n\nIndividualAccountRequest:";

$test = new TestIndividualAccountRequest();

echo "\n\t- create";
$test->create();
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

echo "\n\t- query get and update";
$test->queryGetAndUpdate();
echo " - OK";

echo "\n\t- update replaces address";
$test->updateReplacesAddress();
echo " - OK";

echo "\n\t- status enum";
$test->statusEnum();
echo " - OK";

echo "\n\t- output only fields round trip";
$test->outputOnlyFieldsRoundTrip();
echo " - OK";

echo "\n\t- address is nested object";
$test->addressIsNestedObject();
echo " - OK";

echo "\n\t- error invalid name";
$test->errorInvalidName();
echo " - OK";

echo "\n\t- error invalid taxId";
$test->errorInvalidTaxId();
echo " - OK";

echo "\n\t- error invalid address";
$test->errorInvalidAddress();
echo " - OK";

echo "\n\t- error invalid income";
$test->errorInvalidIncome();
echo " - OK";

echo "\n\t- error invalid status";
$test->errorInvalidStatus();
echo " - OK";

echo "\n\t- error not found";
$test->errorNotFound();
echo " - OK";
