<?php


namespace StarkInfra;

use Exception;

class TestPixDomain
{
    public static function query()
    {
        $domains = iterator_to_array(PixDomain::query(["limit"=>10]));

        foreach ($domains as $domain){
            if (is_null($domain->name)){
                throw new Exception("failed");
            }
        }
    }
}

echo "\n\nPixDomain:";

$test = new TestPixDomain();

echo "\n\t- query";
$test->query();
echo " - OK";
