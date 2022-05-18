<?php

namespace Test\PixInfraction;

use Exception;
use StarkInfra\PixInfraction;
use StarkInfra\Utils\EndToEndId;


class TestPixInfraction
{
    public function create()
    {
        $infraction = PixInfraction::create([TestPixInfraction::example()])[0];
        
        if (is_null($infraction->id)){
            throw new Exception("failed");
        }
    }

    public function queryAndCancel()
    {
        $infractions = iterator_to_array(PixInfraction::query(
            ["status"=>"delivered"]
        ));
        foreach($infractions as $infraction)
        {
            if ($infraction->agent == "reporter") {
                $infractionCanceled = PixInfraction::cancel($infraction->id);

                if ($infraction->id != $infractionCanceled->id) {
                    throw new Exception("failed");
                } 
            }
        } 
    } 
    
    public function queryAndGet()
    {
        $infractions = iterator_to_array(PixInfraction::query(["limit" => 10]));

        if (count($infractions) != 10) {
            throw new Exception("failed");
        }

        $infraction = PixInfraction::get($infractions[0]->id);

        if ($infractions[0]->id != $infraction->id) {
            throw new Exception("failed");
        }
    }

    public function queryIds()
    {
        $infractions = iterator_to_array(PixInfraction::query(["limit" => 10]));
        $infractionsIdsExpected = array();
        for ($i = 0; $i < sizeof($infractions); $i++) {
            array_push($infractionsIdsExpected, $infractions[$i]->id);
        }

        $infractionsResult = iterator_to_array(PixInfraction::query((["ids" => $infractionsIdsExpected])));
        $infractionsIdsResult = array();
        for ($i = 0; $i < sizeof($infractionsResult); $i++) {
            array_push($infractionsIdsResult, $infractionsResult[$i]->id);
        }

        sort($infractionsIdsExpected);
        sort($infractionsIdsResult);

        if ($infractionsIdsExpected != $infractionsIdsResult) {
            throw new Exception("failed");
        }
    }

    public function page(){

        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = PixInfraction::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $pixInfraction) {
                if (in_array($pixInfraction->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $pixInfraction->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 10) {
            throw new Exception("failed");
        }
    }

    public function update()
    {
        $infractions = PixInfraction::query(["status" => "delivered", "limit" => 1]);
        foreach ($infractions as $infraction) {
            if (is_null($infraction->id)) {
                throw new Exception("failed");
            }
            if ($infraction->status != "delivered") {
                throw new Exception("failed");
            }    
            $updatedInfraction = PixInfraction::update($infraction->id, ["result" => "disagreed"]);
            if ($updatedInfraction->result != "disagreed") {
                throw new Exception("failed");
            }    
        }
    }

    public static function example()
    {
        $params = [
            "referenceId" => EndToEndId::create(20018183),
            "type" => "fraud"
        ];
        return new PixInfraction($params);
    }
}

echo "\nPixInfraction:";

$test = new TestPixInfraction();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query and cancel";
$test->queryAndCancel();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- query Ids";
$test->queryIds();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";

echo "\n\t- update";
$test->update();
echo " - OK";
