<?php

namespace Test\CreditNotePreview;
use \DateTime;
use \Exception;
use \DateInterval;
use \DateTimeZone;
use StarkInfra\CreditNote;
use StarkInfra\CreditNotePreview;


class TestCreditNotePreview
{
    public function createCustom()
    {
        $preview = CreditNotePreview::create([TestCreditNotePreview::exampleCustomCreditNotePreview()])[0];

        if (is_null($preview->amount)) {
            throw new Exception("failed");
        }
        if (count($preview->invoices) != $preview->count) {
            throw new Exception("failed");
        }
    }

    public function createAmerican()
    {
        $preview = CreditNotePreview::create([TestCreditNotePreview::exampleAmericanCreditNotePreview()])[0];

        if (is_null($preview->amount)) {
            throw new Exception("failed");
        }
        if (count($preview->invoices) != $preview->count) {
            throw new Exception("failed");
        }
    }

    public function exampleCustomCreditNotePreview()
    {
        $preview = [
            "type" => "custom",
            "nominalAmount" => 100000,
            "scheduled" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P1Y")))->format("Y-m-d"),
            "taxId" => "012.345.678-90",
            "invoices" =>[
                new CreditNote\Invoice([
                    "amount" => 25000,
                    "due" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P1Y1M")))->format("Y-m-d"),
                ]),
                new CreditNote\Invoice([
                    "amount" => 25000,
                    "due" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P2Y2M")))->format("Y-m-d"),
                ]),
                new CreditNote\Invoice([
                    "amount" => 50000,
                    "due" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P2Y5M")))->format("Y-m-d"),
                ])
            ]
        ];
        return new CreditNotePreview($preview);
    }

    public function exampleAmericanCreditNotePreview()
    {
        $preview = [
            "type" => "american",
            "nominalAmount" => 100000,
            "scheduled" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P1Y")))->format("Y-m-d"),
            "taxId" => "012.345.678-90",
            "initialDue" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P1Y2M")))->format("Y-m-d"),
            "nominalInterest" => 10,
            "count" => 5,
            "interval" => "month",
        ];
        return new CreditNotePreview($preview);
    }
}

echo "\n\nCreditNotePreview:";

$test = new TestCreditNotePreview();

echo "\n\t- createCustom";
$test->createCustom();
echo " - OK";

echo "\n\t- createAmerican";
$test->createAmerican();
echo " - OK";
