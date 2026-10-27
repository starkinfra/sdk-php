<?php

namespace Test\IndividualIdentity;
use \Exception;
use StarkInfra\IndividualIdentity;


class TestIndividualIdentity
{
    public function constructAcceptsContractFields()
    {
        $identity = new IndividualIdentity([
            "id" => "5656565656565656",
            "name" => "Tony Stark",
            "email" => "tony@starkindustries.com",
            "deliveryMethod" => "manual",
            "proofs" => ["identity", "biometric"],
            "taxId" => "012.345.678-90",
            "phone" => "+5511987654321",
            "tags" => ["avengers"],
            "validatorLink" => "https://validator.starkinfra.com/?info=abc",
            "sourceId" => "4545454545454545",
            "sourceType" => "external",
            "workspaceId" => "4545454545454545",
            "status" => "created",
            "created" => "2026-06-23T12:00:00.000000+00:00",
            "updated" => "2026-06-23T12:05:30.000000+00:00",
        ]);

        if ($identity->proofs != ["identity", "biometric"] || $identity->validatorLink != "https://validator.starkinfra.com/?info=abc") {
            throw new Exception("failed");
        }
        if (!$identity->updated instanceof \DateTime) {
            throw new Exception("failed");
        }
    }

    public function create()
    {
        $individualIdentity = IndividualIdentity::create([TestIndividualIdentity::exampleIdentity()])[0];
        if (is_null($individualIdentity->id)) {
            throw new Exception("failed");
        }
        if (is_null($individualIdentity->validatorLink) || $individualIdentity->status != "created") {
            throw new Exception("failed");
        }
    }

    public function updateTaxId()
    {
        $example = TestIndividualIdentity::exampleIdentity();
        $example->taxId = null;
        $identity = IndividualIdentity::create([$example])[0];

        $updated = IndividualIdentity::update($identity->id, "012.345.678-90");

        if ($updated->id != $identity->id || preg_replace("/\D/", "", $updated->taxId) != "01234567890") {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $individualIdentitys = iterator_to_array(IndividualIdentity::query(["limit" => 5]));
        
        if (count($individualIdentitys) != 5) {
            throw new Exception("failed");
        }

        $individualIdentity = IndividualIdentity::get($individualIdentitys[0]->id);

        if ($individualIdentitys[0]->id != $individualIdentity->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = IndividualIdentity::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $individualIdentity) {
                if (in_array($individualIdentity->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $individualIdentity->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 4) {
            throw new Exception("failed");
        }
    }

    public function exampleIdentity()
    {
        $params = [
            "name" => "Tony Stark",
            "email" => "tony@starkindustries.com",
            "deliveryMethod" => "manual",
            "proofs" => ["identity"],
            "tags" => [
                'War supply',
                'Invoice #1234'
            ],
            "taxId" => "012.345.678-90",
        ];
        return new IndividualIdentity($params);
    }
}

echo "\n\nIndividualIdentity:";

$test = new TestIndividualIdentity();

echo "\n\t- construct accepts contract fields";
$test->constructAcceptsContractFields();
echo " - OK";

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- update taxId";
$test->updateTaxId();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
