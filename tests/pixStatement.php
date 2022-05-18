<?php

namespace Test\PixStatement;

use \Exception;
use StarkInfra\PixStatement;


class TestPixStatement
{
    public function create()
    {
        $statement = PixStatement::create(TestPixStatement::example());

        if (is_null($statement->id)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $statements = iterator_to_array(PixStatement::query(["limit" => 10]));

        if (count($statements) != 10) {
            throw new Exception("failed");
        }

        $statement = PixStatement::get($statements[0]->id);

        if ($statements[0]->id != $statement->id) {
            throw new Exception("failed");
        }
    }

    public function queryIds()
    {
        $statements = iterator_to_array(PixStatement::query(["limit" => 10]));
        $statementsIdsExpected = array();
        for ($i = 0; $i < sizeof($statements); $i++) {
            array_push($statementsIdsExpected, $statements[$i]->id);
        }

        $statementsResult = iterator_to_array(PixStatement::query((["ids" => $statementsIdsExpected])));
        $statementsIdsResult = array();
        for ($i = 0; $i < sizeof($statementsResult); $i++) {
            array_push($statementsIdsResult, $statementsResult[$i]->id);
        }

        sort($statementsIdsExpected);
        sort($statementsIdsResult);

        if ($statementsIdsExpected != $statementsIdsResult) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = PixStatement::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $pixRequest) {
                if (in_array($pixRequest->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $pixRequest->id);
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
            "after" => "2022-01-01",
            "before" => "2022-01-01",
            "type" => "transaction",
        ];
        return new PixStatement($params);
    }
}

echo "\n\nPixStatement:";

$test = new TestPixStatement();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- query";
$test->queryIds();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
