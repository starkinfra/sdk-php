<?php

namespace Test\PixDirector;
use \Exception;
use StarkInfra\PixDirector;

class TestPixDirector
{
    public function create()
    {   
        $director = PixDirector::create(TestPixDirector::example());
       
        if (is_null($director->id)){
            throw new Exception("failed");
        }
    }

    public static function example()
    {
        $params = [
            "name" => "Eddard Stark",
            "taxId" => "012.345.678-90",
            "phone" => "+55-11998989898", 
            "email" => "eddard@starkbank.com", 
            "password" => "12345678",
            "teamEmail" => "starkfamily@starkbank.com",
            "teamPhones" => ["+55-11997979797", "+55-11996969696"]
        ];
        $testDirector = new PixDirector($params);
        return $testDirector;
    }
}

echo "\n\nPixDirector:";

$test = new TestPixDirector();

echo "\n\t- create";
$test->create();
echo " - OK";
