<?php

namespace Test\PixKeyHolmes;
use \Exception;
use StarkInfra\PixKeyHolmes;


class TestPixKeyHolmes
{
    public function create()
    {
        $pixKeyHolmes = PixKeyHolmes::create([TestPixKeyHolmes::exampleHolmes()])[0];

        // [M1] create returns the entity with a server-assigned id
        if (is_null($pixKeyHolmes->id)) {
            throw new Exception("failed");
        }

        // [M1] server-assigned status is present and non-empty (open set: created|solving|solved|failed)
        if (is_null($pixKeyHolmes->status) || $pixKeyHolmes->status === "") {
            throw new Exception("failed");
        }

        // [M6] created and updated are parsed (non-null) on the create response
        if (is_null($pixKeyHolmes->created)) {
            throw new Exception("failed");
        }
        if (is_null($pixKeyHolmes->updated)) {
            throw new Exception("failed");
        }
    }

    public function query()
    {
        // [M2][M3] list with limit; there is NO get to round-trip against
        $pixKeyHolmes = iterator_to_array(PixKeyHolmes::query(["limit" => 1]));

        if (count($pixKeyHolmes) != 1) {
            throw new Exception("failed");
        }

        // the listed entity carries a parsed id
        if (is_null($pixKeyHolmes[0]->id)) {
            throw new Exception("failed");
        }
    }

    public function queryParams()
    {
        // [M2] every documented query filter serializes through to the wire
        $pixKeyHolmes = iterator_to_array(PixKeyHolmes::query([
            "limit" => 10,
            "after" => "2020-04-01",
            "before" => "2020-04-30",
            "status" => ["solved", "solving"],
            "tags" => ["iron", "suit"],
            "ids" => ["1", "2"],
        ]));

        // absurd filter window => server returns an empty list; passes if params serialize
        if (count($pixKeyHolmes) != 0) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        // [M4] pagination walks an opaque cursor, not a numeric page index
        $ids = [];
        $cursor = null;
        for ($i = 0; $i < 2; $i++) {
            list($page, $cursor) = PixKeyHolmes::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $pixKeyHolmes) {
                if (in_array($pixKeyHolmes->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $pixKeyHolmes->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 4) {
            throw new Exception("failed");
        }
    }

    public static function exampleHolmes()
    {
        // [M5] create accepts ONLY keyId and tags
        // [M7] tags is optional; supplied here for filtering coverage
        $params = [
            "keyId" => "valid@sandbox.com",
            "tags" => [
                "War supply",
                "Invoice #1234",
            ],
        ];
        return new PixKeyHolmes($params);
    }
}

echo "\n\nPixKeyHolmes:";

$test = new TestPixKeyHolmes();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- query params";
$test->queryParams();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
