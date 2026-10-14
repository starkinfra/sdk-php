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

    public function createWithNominalAmountOrAmount()
    {
        $value = rand(100000, 1000000);
        $sent = [];
        foreach (["amount", "nominalAmount"] as $field) {
            foreach (["sac", "price", "american", "bullet"] as $type) {
                array_push($sent, new CreditPreview(["type" => "credit-note", "credit" => $this->exampleAmountCreditNotePreview($type, $field, $value)]));
            }
        }

        $received = CreditPreview::create($sent);
        if (count($received) != count($sent)) {
            throw new Exception("failed");
        }

        foreach ($received as $index => $preview) {
            $sentNote = $sent[$index]->credit;
            $receivedNote = $preview->credit;
            if ($receivedNote->type != $sentNote->type) {
                throw new Exception("failed");
            }
            if (is_null($sentNote->amount)) {
                if ($receivedNote->nominalAmount != $value || $receivedNote->amount <= 0) {
                    throw new Exception("failed");
                }
                continue;
            }
            if ($receivedNote->amount != $value || $receivedNote->nominalAmount <= 0) {
                throw new Exception("failed");
            }
        }
    }

    public function exampleAmountCreditNotePreview($type, $field, $value)
    {
        $preview = [
            "taxId" => "012.345.678-90",
            "type" => $type,
            $field => $value,
            "nominalInterest" => 12.5,
            "scheduled" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P" . rand(11, 19) . "D")))->format("Y-m-d"),
            "initialDue" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P" . rand(30, 39) . "D")))->format("Y-m-d"),
        ];
        // bullet has a single installment, and the API rejects an interval for it
        if ($type != "bullet") {
            $preview["count"] = 12;
            $preview["interval"] = "month";
        }
        return new CreditPreview\CreditNotePreview($preview);
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

echo "\n\t- create with nominalAmount or amount";
$test->createWithNominalAmountOrAmount();
echo " - OK";
