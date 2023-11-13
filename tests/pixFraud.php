<?php

namespace Test\PixFraud;
use \Exception;
use StarkInfra\PixFraud;


class TestPixFraud
{
    public function create()
    {
        $infraction = PixFraud::create([PixFraud::example()])[0];
        
        if (is_null($infraction->id)){
            throw new Exception("failed");
        }
    }

    public function createAndCancel()
    {
        $pixFraud = PixFraud::create([TestPixFraud::example()])[0];
        if (is_null($pixFraud->id)) {
            throw new Exception("failed");
        }
        sleep(3);
        $canceledPixFraud = PixFraud::cancel($pixFraud->id);

        if (is_null($canceledPixFraud->id)) {
            throw new Exception("failed");
        }
    }
    
    public function queryAndGet()
    {
        $frauds = iterator_to_array(PixFraud::query(["limit" => 10]));

        if (count($frauds) != 10) {
            throw new Exception("failed");
        }

        $fraud = PixFraud::get($frauds[0]->id);

        if ($frauds[0]->id != $fraud->id) {
            throw new Exception("failed");
        }
    }

    public function page(){

        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = PixFraud::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $pixFraud) {
                if (in_array($pixFraud->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $pixFraud->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 10) {
            throw new Exception("failed");
        }
    }

    public static function example()
    {
        $params = [
            "externalId" => "my_external_id_123",
            "type" => "mule",
            "taxId" => "01234567890",
        ];
        return new PixFraud($params);
    }
}

echo "\nPixFraud:";

$test = new TestPixFraud();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- create and cancel";
$test->createAndCancel();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";
