<?php

namespace Test\PixUser;
use \Exception;
use StarkInfra\PixUser;


class TestPixUser
{
    public function get()
    {
        $user = PixUser::get("01234567890");

        print_r($user);

        if (is_null($user->statistics)){
            throw new Exception("failed");
        }
    }
}

echo "\nPixInfraction:";

$test = new TestPixUser();

echo "\n\t- get";
$test->get();
echo " - OK";
