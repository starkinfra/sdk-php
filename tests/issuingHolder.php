<?php

namespace Test\IssuingHolder;

use \Exception;
use Test\Utils\Rule;
use StarkInfra\IssuingHolder;

class TestIssuingHolder
{

    public function query()
    {
        $holders = IssuingHolder::query(["limit" => 10, "expand" => ["rules"]]);

        foreach ($holders as $holder) {
            if (is_null($holder->id)) {
                throw new Exception("failed");
            }
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = IssuingHolder::page(["limit" => 2, "expand" => ["rules"], "cursor" => $cursor]);
            foreach ($page as $holder) {
                if (is_null($holder->id) or in_array($holder->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $holder->id);
            }
            if ($cursor == null) {
                break;
            }
        }
    }

    public function get()
    {
        $holder = iterator_to_array(IssuingHolder::query(["limit" => 1]))[0];
        $holder = IssuingHolder::get($holder->id, ["expand" => "rules"]);

        if (!is_string($holder->id)) {
            throw new Exception("failed");
        }
    }

    public function postPatchAndCancel()
    {
        $holders = IssuingHolder::create(TestIssuingHolder::generateExampleHoldersJson(2), ["expand" => "rules"]);
        $holderId = $holders[0]->id;
        $holder = IssuingHolder::update($holderId, ["name" => "Updated Name"]);
        if ($holder->name != "Updated Name") {
            throw new Exception("failed");
        }
        $holder = IssuingHolder::cancel($holderId);
        if ($holder->status != "canceled") {
            throw new Exception("failed");
        }
    }

    public static function generateExampleHoldersJson($n=1)
    {
        $holders = [];
        foreach (range(1, $n) as $index) {
            $holder = new IssuingHolder([
                "name" => "Holder Test",
                "taxId" => "012.345.678-90",
                "externalId" => strval(random_int(1, 999999)),
                "tags" => ["Traveler Employee"],
                "rules" => Rule::generateExampleRulesJson()
            ]);
            array_push($holders, $holder);
        }
        return $holders;
    }
}

echo "\n\nIssuingHolder:";

$test = new TestIssuingHolder();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- post, patch and cancel";
$test->postPatchAndCancel();
echo " - OK";
