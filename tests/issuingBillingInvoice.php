<?php

namespace Test\IssuingBillingInvoice;
use \Exception;
use \DateTime;
use StarkInfra\IssuingBillingInvoice;


class TestIssuingBillingInvoice
{
    public function queryAndGet()
    {
        $invoices = iterator_to_array(IssuingBillingInvoice::query(["limit" => 10]));

        foreach ($invoices as $invoice) {
            if (is_null($invoice->id)) {
                throw new Exception("failed");
            }
        }

        if (count($invoices) == 0) {
            return;
        }

        $invoice = IssuingBillingInvoice::get($invoices[0]->id);

        if ($invoices[0]->id != $invoice->id) {
            throw new Exception("failed");
        }
    }

    public function queryParams()
    {
        $invoices = iterator_to_array(IssuingBillingInvoice::query([
            "limit" => 10,
            "after" => "2020-04-01",
            "before" => "2020-04-30",
            "status" => ["paid", "expired"],
            "tags" => ["iron", "suit"],
            "ids" => ["1", "2"],
        ]));

        if (count($invoices) != 0) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i = 0; $i < 2; $i++) {
            list($page, $cursor) = IssuingBillingInvoice::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $invoice) {
                if (in_array($invoice->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $invoice->id);
            }
            if ($cursor == null) {
                break;
            }
        }
    }

    public function datetimeFields()
    {
        $invoices = iterator_to_array(IssuingBillingInvoice::query(["limit" => 10]));

        foreach ($invoices as $invoice) {
            foreach (["due", "start", "end", "created", "updated"] as $field) {
                if (!property_exists($invoice, $field)) {
                    throw new Exception("failed");
                }
                if (!is_null($invoice->$field) && !($invoice->$field instanceof DateTime)) {
                    throw new Exception("failed");
                }
            }
        }
    }
}

echo "\n\nIssuingBillingInvoice:";

$test = new TestIssuingBillingInvoice();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- query params";
$test->queryParams();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";

echo "\n\t- datetime fields";
$test->datetimeFields();
echo " - OK";
