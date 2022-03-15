<?php

namespace Test\IssuingBin;

use \Exception;
use StarkInfra\IssuingBin;


class TestIssuingBin
{

    public function query()
    {
        $bins = IssuingBin::query();

        foreach ($bins as $bin) {
            if (is_null($bin->id)) {
                throw new Exception("failed");
            }
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = IssuingBin::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $bin) {
                if (is_null($bin->id) or in_array($bin->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $bin->id);
            }
            if ($cursor == null) {
                break;
            }
        }
    }
}

echo "\n\nIssuingBin:";

$test = new TestIssuingBin();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";
