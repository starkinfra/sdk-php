<?php

namespace Test\CreditHolmes;
use \Exception;
use StarkInfra\CreditHolmes;


class TestCreditHolmes
{
    public function create()
    {
        $creditHolmes = CreditHolmes::create([TestCreditHolmes::exampleHolmes()])[0];
        if (is_null($creditHolmes->id)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $creditHolmes = iterator_to_array(CreditHolmes::query(["limit" => 1]));
        
        if (count($creditHolmes) != 1) {
            throw new Exception("failed");
        }

        $sherlock = CreditHolmes::get($creditHolmes[0]->id);
        if ($creditHolmes[0]->id != $sherlock->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = CreditHolmes::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $creditHolmes) {
                if (in_array($creditHolmes->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $creditHolmes->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 4) {
            throw new Exception("failed");
        }
    }

    public function exampleHolmes()
    {
        $params = [
            "taxId" => "012.345.678-90",
            "competence" => "2022-09",
            "tags" => [
                'War supply',
                'Invoice #1234'
            ],
        ];
        return new CreditHolmes($params);
    }
}

echo "\n\nCreditHolmes:";

$test = new TestCreditHolmes();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
