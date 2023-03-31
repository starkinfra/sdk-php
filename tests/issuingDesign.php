<?php

namespace Test\IssuingDesign;
use \Exception;
use StarkInfra\IssuingDesign;

class TestIssuingDesign
{

    public function queryAndGet()
    {
        $designs = iterator_to_array(IssuingDesign::query(["limit" => 1]));
        print_r($designs);
        if (count($designs) != 1) {
            throw new Exception("failed");
        }

        $design = IssuingDesign::get($designs[0]->id);

        if ($designs[0]->id != $design->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = IssuingDesign::page($options = ["limit" => 1, "cursor" => $cursor]);
            foreach ($page as $design) {
                if (in_array($design->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $design->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 2) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nIssuingDesign:";

$test = new TestIssuingDesign();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
