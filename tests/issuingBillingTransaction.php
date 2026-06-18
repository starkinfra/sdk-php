<?php

namespace Test\IssuingBillingTransaction;
use \Exception;
use \DateTime;
use StarkInfra\IssuingBillingTransaction;
use StarkInfra\IssuingBillingInvoice;


class TestIssuingBillingTransaction
{
    public function query()
    {
        $transactions = iterator_to_array(IssuingBillingTransaction::query(["limit" => 10]));

        foreach ($transactions as $transaction) {
            if (is_null($transaction->id)) {
                throw new Exception("failed");
            }
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i = 0; $i < 2; $i++) {
            list($page, $cursor) = IssuingBillingTransaction::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $transaction) {
                if (in_array($transaction->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $transaction->id);
            }
            if ($cursor == null) {
                break;
            }
        }
    }

    public function queryParams()
    {
        $invoices = iterator_to_array(IssuingBillingInvoice::query(["limit" => 1]));

        $options = [
            "limit" => 10,
            "after" => "2020-04-01",
            "before" => "2020-04-30",
            "tags" => ["iron", "suit"],
        ];

        if (count($invoices) != 0) {
            $options["invoiceId"] = $invoices[0]->id;
        }

        $transactions = iterator_to_array(IssuingBillingTransaction::query($options));

        if (count($transactions) != 0) {
            throw new Exception("failed");
        }
    }

    public function fields()
    {
        $transactions = iterator_to_array(IssuingBillingTransaction::query(["limit" => 10]));

        $expected = [
            "id", "amount", "invoiceId", "installment", "installmentCount", "balance",
            "holderName", "source", "externalId", "description", "cardEnding", "tax",
            "rate", "merchantAmount", "merchantCurrencyCode", "created",
        ];

        foreach ($transactions as $transaction) {
            foreach ($expected as $field) {
                if (!property_exists($transaction, $field)) {
                    throw new Exception("failed");
                }
            }
            if (!is_null($transaction->created) && !($transaction->created instanceof DateTime)) {
                throw new Exception("failed");
            }
        }
    }
}

echo "\n\nIssuingBillingTransaction:";

$test = new TestIssuingBillingTransaction();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";

echo "\n\t- query params";
$test->queryParams();
echo " - OK";

echo "\n\t- fields";
$test->fields();
echo " - OK";
