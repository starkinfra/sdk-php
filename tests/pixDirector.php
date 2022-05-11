<?php

namespace Test\PixDirector;
use \Exception;
use StarkInfra\PixDirector;
use StarkInfra\Error\InvalidSignatureError;

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
            "name" => "Matheus Ferraz",
            "taxId" => "012.345.678-90",
            "phone" => "+55-1141164616", 
            "email" => "bacen@starkbank.com", 
            "password" => "12345678",
            "teamEmail" => "bacen@starkbank.com",
            "teamPhones" => ["+55-1141164616"]
        ];
        $testDirector = new PixDirector($params);
        print_r($testDirector);
        return $testDirector;
        
    }
}

echo "\n\nPixDirector:";

$test = new TestPixDirector();

echo "\n\t- create";
$test->create();
echo " - OK";