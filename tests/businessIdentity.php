<?php

namespace Test\BusinessIdentity;
use \Exception;
use StarkInfra\BusinessIdentity;


class TestBusinessIdentity
{
    public function create()
    {
        $businessIdentity = BusinessIdentity::create([TestBusinessIdentity::exampleIdentity()])[0];
        if (is_null($businessIdentity->id)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $businessIdentitys = iterator_to_array(BusinessIdentity::query(["limit" => 5]));

        if (count($businessIdentitys) != 5) {
            throw new Exception("failed");
        }

        $businessIdentity = BusinessIdentity::get($businessIdentitys[0]->id);

        if ($businessIdentitys[0]->id != $businessIdentity->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = BusinessIdentity::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $businessIdentity) {
                if (in_array($businessIdentity->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $businessIdentity->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 4) {
            throw new Exception("failed");
        }
    }

    public function update()
    {
        $businessIdentity = BusinessIdentity::create([TestBusinessIdentity::exampleIdentity()])[0];

        $businessIdentity = BusinessIdentity::update($businessIdentity->id, ["tags" => ["new", "tags"]]);

        if ($businessIdentity->tags != ["new", "tags"]) {
            throw new Exception("failed");
        }
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

echo "\n\nBusinessIdentity:";

$test = new TestBusinessIdentity();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";

echo "\n\t- update";
$test->update();
echo " - OK";
