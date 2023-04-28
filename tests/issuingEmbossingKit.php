<?php

namespace Test\IssuingEmbossingKit;
use \Exception;
use StarkInfra\IssuingEmbossingKit;

class TestIssuingEmbossingKit
{

    public function queryAndGet()
    {
        $kits = iterator_to_array(IssuingEmbossingKit::query(["limit" => 1]));
        if (count($kits) != 1) {
            throw new Exception("failed");
        }

        $kit = IssuingEmbossingKit::get($kits[0]->id);

        if ($kits[0]->id != $kit->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = IssuingEmbossingKit::page($options = ["limit" => 1, "cursor" => $cursor]);
            foreach ($page as $kit) {
                if (in_array($kit->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $kit->id);
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

echo "\n\nIssuingEmbossingKit:";

$test = new TestIssuingEmbossingKit();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
