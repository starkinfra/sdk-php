<?php

namespace Test\Utils;
use StarkInfra\Ledger;
use StarkInfra\Ledger\Rule;


class LedgerGenerator
{
    public static function generateExampleLedgersJson($n=1)
    {
        $ledgers = [];
        foreach (range(1, $n) as $index) {
            $ledger = new Ledger([
                "externalId" => strval(random_int(1, 999999)),
                "tags" => ["savings account", "spending counter"],
                "metadata" => ["accountId" => "123"],
                "rules" => [new Rule([
                    "key" => "minimumBalance",
                    "value" => 0
                ])]
            ]);
            array_push($ledgers, $ledger);
        }
        return $ledgers;
    }
}
