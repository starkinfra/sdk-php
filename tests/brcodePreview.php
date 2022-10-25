<?php

namespace Test\BrcodePreview;
use \Exception;
use StarkInfra\BrcodePreview;
use StarkInfra\DynamicBrcode;
use StarkInfra\StaticBrcode;


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
            new BrcodePreview(["id" => $brcodeIdList[0]]),
            new BrcodePreview(["id" => $brcodeIdList[1]]),
            new BrcodePreview(["id" => $brcodeIdList[2]]),
            new BrcodePreview(["id" => $brcodeIdList[3]])
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
}

echo "\n\nBrcode Preview:";

$test = new TestBrcodePreview();

echo "\n\t- create";
$test->create();
echo " - OK";
