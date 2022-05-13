<?php

namespace Test\PixKey;
use \Exception;
use StarkInfra\PixKey;

class TestPixKey
{
    public function createAndCancel()
    {
        $keys = PixKey::create(TestPixKey::example());
        if (is_null($keys->id)){
            throw new Exception("failed");
        }

        $keyId = $keys[0]->id;

        $key = PixKey::cancel($keyId);
        if ($key->status != "canceled") {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $keys = iterator_to_array(PixKey::query(["limit"=>10]));

        if (count($keys) != 10){
            throw new Exception("failed");
        }

        $key = PixKey::get($keys[0]->id);

        if($keys[0]->id != $key->id){
            throw new Exception("failed");
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = PixKey::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $pixKey) {
                if (in_array($pixKey->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $pixKey->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 10) {
            throw new Exception("failed");
        }
    }

    public function update()
    {
        $keys = PixKey::query(["status" => "active", "limit" => 1]);
        foreach ($keys as $key) {
            if (is_null($key->id)) {
                throw new Exception("failed");
            }
            if ($key->status != "active") {
                throw new Exception("failed");
            }    
            $updateKey = PixKey::update($key->id, ["status" => "blocked"]);
            if ($updateKey->status != "blocked") {
                throw new Exception("failed");
            }    
        }
    }

    public static function example()
    {
        $params = [
            "accountCreated" => "2022-01-01",
            "accountNumber" => "76543",
            "accountType" => "salary",
            "branchCode" => "1234",
            "name" => "Jamie Lannister",
            "taxId" => "012.345.678-90"
        ];
        return new PixKey($params);
    }
}

echo "\n\nPixKey:";

$test = new TestPixKey();

echo "\n\t- create and cancel";
$test->createAndCancel();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->page();
echo " - OK";

echo "\n\t- update";
$test->update();
echo " - OK";