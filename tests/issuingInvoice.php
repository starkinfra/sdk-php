<?php

namespace Test\IssuingInvoice;

use \Exception;
use StarkInfra\IssuingInvoice;


class TestIssuingInvoice
{

    public function query()
    {
        $invoices = IssuingInvoice::query(["limit" => 10]);

        foreach ($invoices as $invoice) {
            if (is_null($invoice->id)) {
                throw new Exception("failed");
            }
        }
    }

    public function get()
    {
        $invoice = iterator_to_array(IssuingInvoice::query(["limit" => 1]))[0];
        $invoice = IssuingInvoice::get($invoice->id);

        if (!is_string($invoice->id)) {
            throw new Exception("failed");
        }
    }

    public function create()
    {
        $invoice = self::generateExampleInvoice();
        $invoice = IssuingInvoice::create($invoice);

        if (is_null($invoice->id)) {
            throw new Exception("failed");
        }
    }

    public static function generateExampleInvoice()
    {
        return new IssuingInvoice([
            "amount" => 400000,
            "taxId" => "012.345.678-90",
            "name" => "João Rosá",
            "tags" => [
                'War supply',
                'Invoice #1234'
            ],
        ]);
    }
}

echo "\n\nIssuingInvoice:";

$test = new TestIssuingInvoice();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- get";
$test->get();
echo " - OK";

echo "\n\t- create";
$test->create();
echo " - OK";
