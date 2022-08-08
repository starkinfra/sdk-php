<?php

namespace Test\CreditPreview;

use COM;
use \Exception;
use \DateTime;
use \DateInterval;
use StarkInfra\CreditPreview;
use \DateTimeZone;
use StarkInfra\CreditNote;


class TestCreditPreview
{

    public function create()
    {

        $previews = [];
        array_push($previews, new CreditPreview(["type" => "credit-note", "credit" => $this->exampleBulletCreditNotePreview()]));
        array_push($previews, new CreditPreview(["type" => "credit-note", "credit" => $this->exampleSacCreditNotePreview()]));
        array_push($previews, new CreditPreview(["type" => "credit-note", "credit" => $this->exampleCustomCreditNotePreview()]));
        array_push($previews, new CreditPreview(["type" => "credit-note", "credit" => $this->exampleCustomCreditNotePreview()]));
        $previews = CreditPreview::create($previews);
        foreach ($previews as $preview) {
            if ($preview == null) {
                throw new Exception("failed");
            }
        }
    }

    public function exampleBulletCreditNotePreview()
    {
        $preview = [
            "taxId" => "012.345.678-90",
            "type" => "bullet",
            "nominalAmount" => 100000,
            "rebateAmount" => 900,
            "nominalInterest" => 10,
            "scheduled" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P1Y")))->format("Y-m-d"),
            "initialDue" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P2Y2M")))->format("Y-m-d"),
        ];
        return new CreditPreview\CreditNotePreview($preview);
    }

    public function exampleSacCreditNotePreview()
    {
        $preview = [
            "taxId" => "012.345.678-90",
            "type" => "sac",
            "nominalAmount" => 100000,
            "rebateAmount" => 900,
            "nominalInterest" => 10,
            "scheduled" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P1Y")))->format("Y-m-d"),
            "initialDue" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P2Y2M")))->format("Y-m-d"),
            "initialAmount" => 100,
            "interval" => "month",
        ];
        return new CreditPreview\CreditNotePreview($preview);
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
        return new CreditPreview\CreditNotePreview($preview);
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
        return new CreditPreview\CreditNotePreview($preview);
    }
}

echo "\n\nPayment Preview:";

$test = new TestCreditPreview();

echo "\n\t- create";
$test->create();
echo " - OK";
