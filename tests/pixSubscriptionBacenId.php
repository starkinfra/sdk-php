<?php

namespace Test\PixSubscriptionBacenId;
use \Exception;
use StarkInfra\Utils\BacenId;
use StarkInfra\Utils\EndToEndId;
use StarkInfra\Utils\ReturnId;
use StarkInfra\Utils\PixSubscriptionBacenId;


class TestPixSubscriptionBacenId
{
    public function format()
    {
        $bacenId = PixSubscriptionBacenId::create("32160637", "RR");

        if (strlen($bacenId) != 29) {
            throw new Exception("failed");
        }

        if (!preg_match('/^RR32160637\d{8}[a-zA-Z0-9]{11}$/', $bacenId)) {
            throw new Exception("failed");
        }

        if (substr($bacenId, 10, 8) != date("Ymd")) {
            throw new Exception("failed");
        }
    }

    public function randomPartDiffers()
    {
        $first = PixSubscriptionBacenId::create("32160637", "RR");
        $second = PixSubscriptionBacenId::create("32160637", "RR");

        if ($first == $second) {
            throw new Exception("failed");
        }
    }

    public function endToEndIdAndReturnIdKeepMinutePrecision()
    {
        $endToEndId = EndToEndId::create("32160637");
        $returnId = ReturnId::create("32160637");

        if (strlen($endToEndId) != 32 || strlen($returnId) != 32) {
            throw new Exception("failed");
        }

        if (!preg_match('/^E32160637\d{12}[a-zA-Z0-9]{11}$/', $endToEndId)) {
            throw new Exception("failed");
        }

        if (!preg_match('/^D32160637\d{12}[a-zA-Z0-9]{11}$/', $returnId)) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nPixSubscriptionBacenId:";

$test = new TestPixSubscriptionBacenId();

echo "\n\t- format";
$test->format();
echo " - OK";

echo "\n\t- random part differs";
$test->randomPartDiffers();
echo " - OK";

echo "\n\t- endToEndId and returnId keep minute precision";
$test->endToEndIdAndReturnIdKeepMinutePrecision();
echo " - OK";
