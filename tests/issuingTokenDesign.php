<?php

namespace Test\IssuingTokenDesign;
use \Exception;
use StarkInfra\IssuingTokenDesign;


class TestIssuingTokenDesign
{
    public function queryAndGet()
    {
        $designs = IssuingTokenDesign::query(["limit" => 1]);
        
        foreach ($designs as $design) {

            if (is_null($design->id)) {
                throw new Exception("failed");
            }

            $design = iterator_to_array(IssuingTokenDesign::query(["limit" => 1]))[0];
            $design = IssuingTokenDesign::get($design->id);

            if (!is_string($design->id)) {
                throw new Exception("failed");
            }
        }    
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = IssuingTokenDesign::page($options = ["limit" => 1, "cursor" => $cursor]);
            foreach ($page as $issuingTokenDesign) {
                if (in_array($issuingTokenDesign->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $issuingTokenDesign->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 0) {
            throw new Exception("failed");
        }
    }

    public function pdf()
    {
        $designs = IssuingTokenDesign::query(["limit" => 1]);

        foreach ($designs as $design) {
            $design = IssuingTokenDesign::pdf($design->id);
            if (is_null($design->id)) {
                throw new Exception("failed");
            }
        }
    }

}

echo "\n\nIssuingTokenDesign:";

$test = new TestIssuingTokenDesign();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";

echo "\n\t- get pdf";
$test->pdf();
echo " - OK";