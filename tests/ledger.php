<?php

namespace Test\Ledger;
use \Exception;
use StarkInfra\Ledger;
use StarkInfra\Ledger\Rule;
use Test\Utils\LedgerGenerator;


class TestLedger
{
    public function create()
    {
        $ledgers = Ledger::create(LedgerGenerator::generateExampleLedgersJson(3));
        foreach ($ledgers as $ledger) {
            $checkLedger = Ledger::get($ledger->id);
            if ($ledger->id != $checkLedger->id) {
                throw new Exception("failed");
            }
        }
    }

    public function query()
    {
        $ledgers = Ledger::query(["limit" => 10]);

        foreach ($ledgers as $ledger) {
            if (is_null($ledger->id)) {
                throw new Exception("failed");
            }
        }
    }

    public function page()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = Ledger::page(["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $ledger) {
                if (is_null($ledger->id) or in_array($ledger->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $ledger->id);
            }
            if ($cursor == null) {
                break;
            }
        }
    }

    public function get()
    {
        $ledger = iterator_to_array(Ledger::query(["limit" => 1]))[0];
        $ledger = Ledger::get($ledger->id);

        if (!is_string($ledger->id)) {
            throw new Exception("failed");
        }
    }

    public function update()
    {
        $ledger = iterator_to_array(Ledger::query(["limit" => 1]))[0];
        $tags = ["account/123", "updated-" . strval(rand(0, 1000000))];
        $metadata = ["accountId" => "123", "accountType" => "savings"];
        $updatedLedger = Ledger::update($ledger->id, [
            "rules" => [
                new Rule(["key" => "minimumBalance", "value" => 0])
            ],
            "tags" => $tags,
            "metadata" => $metadata
        ]);
        if (!is_string($updatedLedger->id)) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nLedger:";

$test = new TestLedger();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- update";
$test->update();
echo " - OK";
