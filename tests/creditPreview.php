<?php

namespace Test\CreditPreview;
use \Exception;
use \DateTime;
use \DateInterval;
use \DateTimeZone;
use StarkInfra\CreditPreview;
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
            "nominalAmount" => rand(1, 100000),
            "rebateAmount" => rand(1, 1000),
            "nominalInterest" => rand(1, 10),
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
            "nominalAmount" => rand(1, 100000),
            "rebateAmount" => rand(1, 1000),
            "nominalInterest" => rand(1, 10),
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
            "nominalAmount" => rand(1, 100000),
            "scheduled" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P1Y")))->format("Y-m-d"),
            "taxId" => "012.345.678-90",
            "invoices" =>[
                new CreditNote\Invoice([
                    "amount" => rand(1000, 100000),
                    "due" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P2Y1M")))->format("Y-m-d"),
                ]),
                new CreditNote\Invoice([
                    "amount" => rand(1000, 100000),
                    "due" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P2Y2M")))->format("Y-m-d"),
                ]),
                new CreditNote\Invoice([
                    "amount" => rand(1000, 100000),
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
            "nominalAmount" => rand(1, 100000),
            "scheduled" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P1Y")))->format("Y-m-d"),
            "taxId" => "012.345.678-90",
            "initialDue" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P1Y2M")))->format("Y-m-d"),
            "nominalInterest" => rand(1, 10),
            "count" => 5,
            "interval" => ['month', 'year'][rand(0, 1)],
        ];
        return new CreditPreview\CreditNotePreview($preview);
    }
}

echo "\nCredit Preview:";

$test = new TestCreditPreview();

echo "\n\t- create";
$test->create();
echo " - OK";
