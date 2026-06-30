<?php

namespace Test\LedgerTransaction;
use \Exception;
use StarkInfra\Ledger;
use StarkInfra\LedgerTransaction;
use Test\Utils\LedgerTransactionGenerator;


class TestLedgerTransaction
{
    public function create()
    {
        $transactions = LedgerTransaction::create(LedgerTransactionGenerator::generateExampleTransactionsJson(3));
        foreach ($transactions as $transaction) {
            $checkTransaction = LedgerTransaction::get($transaction->id);
            if ($transaction->id != $checkTransaction->id) {
                throw new Exception("failed");
            }
        }
    }

    public function query()
    {
        $ledger = iterator_to_array(Ledger::query(["limit" => 1]))[0];
        $transactions = LedgerTransaction::query(["ledgerId" => $ledger->id, "limit" => 5]);

        foreach ($transactions as $transaction) {
            if (is_null($transaction->id)) {
                throw new Exception("failed");
            }
        }
    }

    public function page()
    {
        $ledger = iterator_to_array(Ledger::query(["limit" => 1]))[0];
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = LedgerTransaction::page(["ledgerId" => $ledger->id, "limit" => 2, "cursor" => $cursor]);
            foreach ($page as $transaction) {
                if (is_null($transaction->id) or in_array($transaction->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $transaction->id);
            }
            if ($cursor == null) {
                break;
            }
        }
    }

    public function get()
    {
        $ledger = iterator_to_array(Ledger::query(["limit" => 1]))[0];
        $transaction = iterator_to_array(LedgerTransaction::query(["ledgerId" => $ledger->id, "limit" => 1]))[0];
        $transaction = LedgerTransaction::get($transaction->id);

        if (!is_string($transaction->id)) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nLedgerTransaction:";

$test = new TestLedgerTransaction();

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
