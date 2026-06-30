<?php

namespace Test\Utils;
use StarkInfra\Ledger;
use StarkInfra\Ledger\Rule;
use StarkInfra\LedgerTransaction;


class LedgerTransactionGenerator
{
    public static function generateExampleTransactionsJson($n=1)
    {
        $transactions = [];
        $ledgers = iterator_to_array(Ledger::query(["limit" => $n]));
        foreach (range(1, $n) as $index) {
            $ledger = $ledgers[($index - 1) % count($ledgers)];
            $transaction = new LedgerTransaction([
                "amount" => random_int(1000, 9999),
                "ledgerId" => $ledger->id,
                "source" => "account/" . str_pad(strval(random_int(1, 999999)), 6, "0", STR_PAD_LEFT),
                "externalId" => str_pad(strval(random_int(1, 999999)), 6, "0", STR_PAD_LEFT),
                "tags" => ["savings account", "spending counter"],
                "metadata" => ["accountId" => "123"],
                "rules" => [new Rule([
                    "key" => "minimumBalance",
                    "value" => 0
                ])]
            ]);
            array_push($transactions, $transaction);
        }
        return $transactions;
    }
}
