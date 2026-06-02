<?php

namespace Test\BrcodePreview;
use DateTime;
use \Exception;
use \Test\Utils\UtilsDynamicBrcode;
use StarkInfra\StaticBrcode;
use StarkInfra\BrcodePreview;
use StarkInfra\DynamicBrcode;


class TestBrcodePreview
{
    public function create()
    {
        $staticBrcodes = iterator_to_array(StaticBrcode::query(["limit" => 2]));

        $dynamicBrcodes = iterator_to_array(DynamicBrcode::query(["limit" => 2]));
        
        $brcodes = array_merge($staticBrcodes, $dynamicBrcodes);

        $brcodeIdList = [];
        foreach ($brcodes as $brcode) {
            array_push($brcodeIdList, $brcode->id);
        }

        $previews = BrcodePreview::create([
            new BrcodePreview(["id" => $brcodeIdList[0], "payerId" => "20.018.183/0001-80"]),
            new BrcodePreview(["id" => $brcodeIdList[1], "payerId" => "20.018.183/0001-80"]),
            new BrcodePreview(["id" => $brcodeIdList[2], "payerId" => "20.018.183/0001-80"]),
            new BrcodePreview(["id" => $brcodeIdList[3], "payerId" => "20.018.183/0001-80"]),
        ]);

        if (count($previews) != 4) {
            throw new Exception("failed");
        }

        $index = 0;
        foreach ($previews as $preview) {
            if ($preview->id != $brcodeIdList[$index]) {
                throw new Exception("failed");
            }
            $index++;
        }
    }

    public function createPreviewFromInstantBrcode()
    {
        $type = "instant";
        $createdDynamicBrcode = UtilsDynamicBrcode::createDynamicBrcodeByType($type);
        $preview = TestBrcodePreview::createBrcodePreviewById($createdDynamicBrcode->id); 

        if ($preview->id != $createdDynamicBrcode->id) {
            throw new Exception("failed");
        }
        if ($preview->due instanceof DateTime) {
            throw new Exception("failed");
        }
        if ($preview->subscription != null) {
            throw new Exception("failed");
        }
    }

    public function createPreviewFromDueBrcode()
    {
        $type = "due";
        $createdDynamicBrcode = UtilsDynamicBrcode::createDynamicBrcodeByType($type);
        $preview = TestBrcodePreview::createBrcodePreviewById($createdDynamicBrcode->id);

        if ($preview->id != $createdDynamicBrcode->id) {
            throw new Exception("failed");
        }
        if (!$preview->due instanceof DateTime) {
            throw new Exception("failed");
        }
        if ($preview->subscription != null) {
            throw new Exception("failed");
        }
    }

    public function createPreviewFromSubscriptionBrcode()
    {
        $type = "subscription";
        $createdDynamicBrcode = UtilsDynamicBrcode::createDynamicBrcodeByType($type);
        $preview = TestBrcodePreview::createBrcodePreviewById($createdDynamicBrcode->id);

        if ($preview->id != $createdDynamicBrcode->id) {
            throw new Exception("failed");
        }
        if ($preview->payerId != "") {
            throw new Exception("failed");
        }
        if ($preview->subscription->type != "qrcode") {
            throw new Exception("failed");
        }
    }

    public function createPreviewFromSubscriptionAndInstantBrcode()
    {
        $type = "subscriptionAndInstant";
        $createdDynamicBrcode = UtilsDynamicBrcode::createDynamicBrcodeByType($type);
        $preview = TestBrcodePreview::createBrcodePreviewById($createdDynamicBrcode->id);

        if ($preview->id != $createdDynamicBrcode->id) {
            throw new Exception("failed");
        }
        if ($preview->payerId == "") {
            throw new Exception("failed");
        }
        if ($preview->subscription->type != "qrcodeAndPayment") {
            throw new Exception("failed");
        }
    }

    public function createPreviewFromDueAndOrSubscriptionBrcode()
    {
        $type = "dueAndOrSubscription";
        $createdDynamicBrcode = UtilsDynamicBrcode::createDynamicBrcodeByType($type);
        $preview = TestBrcodePreview::createBrcodePreviewById($createdDynamicBrcode->id);

        if ($preview->id != $createdDynamicBrcode->id) {
            throw new Exception("failed");
        }
        if ($preview->payerId == "") {
            throw new Exception("failed");
        }
        if ($preview->subscription->type != "paymentAndOrQrcode") {
            throw new Exception("failed");
        }
    }

    public static function createBrcodePreviewById($id)
    {
        return BrcodePreview::create([
            new BrcodePreview(["id" => $id, "payerId" => "20.018.183/0001-80"]),
        ])[0];
    }
}

echo "\n\nBrcode Preview:";

$test = new TestBrcodePreview();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- create br code preview from instant dynamic brcode";
$test->createPreviewFromInstantBrcode();
echo " - OK";

echo "\n\t- create br code preview from due dynamic brcode";
$test->createPreviewFromDueBrcode();
echo " - OK";

echo "\n\t- create br code preview from subscription dynamic brcode";
$test->createPreviewFromSubscriptionBrcode();
echo " - OK";

echo "\n\t- create br code preview from subscriptionAndInstant dynamic brcode";
$test->createPreviewFromSubscriptionAndInstantBrcode();
echo " - OK";

echo "\n\t- create br code preview from dueAndOrSubscription dynamic brcode";
$test->createPreviewFromDueAndOrSubscriptionBrcode();
echo " - OK";