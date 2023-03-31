<?php

namespace Test\IndividualIdentity;
use \Exception;
use StarkInfra\IndividualIdentity;


class TestIndividualIdentity
{
    public function create()
    {
        $individualIdentity = IndividualIdentity::create([TestIndividualIdentity::exampleIdentity()])[0];
        if (is_null($individualIdentity->id)) {
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

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
