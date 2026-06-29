<?php

namespace Test\CreditNote;
use \DateTime;
use \Exception;
use \DateTimeZone;
use \DateInterval;
use StarkInfra\CreditNote;
use StarkInfra\CreditNote\Rule;
use StarkInfra\CreditSigner;


class TestCreditNote
{
    public function createAndCancel()
    {
        $creditNote = CreditNote::create([TestCreditNote::exampleCCB()])[0];
        if (is_null($creditNote->id)) {
            throw new Exception("failed");
        }

        $canceledNote = CreditNote::cancel($creditNote->id);

        if (is_null($canceledNote->id)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $creditNotes = iterator_to_array(CreditNote::query(["limit" => 5]));
        
        if (count($creditNotes) != 5) {
            throw new Exception("failed");
        }

        $creditNote = CreditNote::get($creditNotes[0]->id);

        if ($creditNotes[0]->id != $creditNote->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = CreditNote::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $creditNote) {
                if (in_array($creditNote->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $creditNote->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 4) {
            throw new Exception("failed");
        }
    }

    public function ruleConstruct()
    {
        $rule = new CreditNote\Rule([
            "key" => "invoiceCreationMode",
            "value" => "scheduled"
        ]);

        if ($rule->key != "invoiceCreationMode") {
            throw new Exception("failed");
        }
        if ($rule->value != "scheduled") {
            throw new Exception("failed");
        }

        $reachable = new \StarkInfra\CreditNote\Rule([
            "key" => "invoiceCreationMode",
            "value" => "scheduled"
        ]);

        if ($reachable->key != "invoiceCreationMode" | $reachable->value != "scheduled") {
            throw new Exception("failed");
        }
    }

    public function createWithRules()
    {
        $creditNote = CreditNote::create([TestCreditNote::exampleCCB()])[0];
        if (is_null($creditNote->id)) {
            throw new Exception("failed");
        }

        if (count($creditNote->rules) == 0) {
            throw new Exception("failed");
        }

        foreach ($creditNote->rules as $rule) {
            if (!($rule instanceof CreditNote\Rule)) {
                throw new Exception("failed");
            }
            if (is_null($rule->key) | is_null($rule->value)) {
                throw new Exception("failed");
            }
        }

        if ($creditNote->rules[0]->key != "invoiceCreationMode") {
            throw new Exception("failed");
        }
        if ($creditNote->rules[0]->value != "scheduled") {
            throw new Exception("failed");
        }
    }

    public function debtorWorkspaceId()
    {
        $creditNote = CreditNote::create([TestCreditNote::exampleCCB()])[0];
        if (is_null($creditNote->id)) {
            throw new Exception("failed");
        }

        if (!property_exists($creditNote, "debtorWorkspaceId")) {
            throw new Exception("failed");
        }
    }

    public function exampleCCB()
    {
        $params = [
            "templateId" => $_SERVER["SANDBOX_TEMPLATE_ID"],
            "name" => "Jamie Lannister",
            "taxId" => "012.345.678-90",
            "nominalAmount" => 100000,
            "scheduled" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P2Y")))->format("Y-m-d"),
            "payment" => new CreditNote\Transfer([
                "bankCode" => "60701190",
                "branchCode" => "7248",
                "accountNumber" => "5005482-1",
                "taxId" => "594.739.480-42", 
                "name" => "Jamie Lannister"
            ]),
            "paymentType" => "transfer",
            "invoices" =>[
                new CreditNote\Invoice([
                    "amount" => 120000,
                    "due" => ((new DateTime("now", new DateTimeZone('Europe/London')))->add(new DateInterval("P2Y3M")))->format("Y-m-d"),
                    "descriptions" => [
                        new CreditNote\Invoice\Description([
                            "key" => "key",
                            "value" => "value"
                        ])
                    ]
                ])
            ], 
            "signers" =>[
                new CreditSigner([
                    "contact" =>  "jamie.lannister@gmail.com",
                    "method" => "link",
                    "name" => "Jamie Lannister",
                ])
            ],
            "rebateAmount" => 0,
            "tags" => [
                'War supply',
                'Invoice #1234'
            ],
            "rules" => [
                new CreditNote\Rule([
                    "key" => "invoiceCreationMode",
                    "value" => "scheduled"
                ])
            ],
            "externalId" => "php-".$uuid = mt_rand(0, 0xffffffff),
            "streetLine1" => "Rua ABC",
            "streetLine2" => "Ap 123",
            "district" => "Jardim Paulista",
            "city" => "São Paulo",
            "stateCode" => "SP",
            "zipCode" => "01234-567",
        ];
        return new CreditNote($params);
    }
}

echo "\n\nCreditNote:";

$test = new TestCreditNote();

echo "\n\t- create and cancel";
$test->createAndCancel();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";

echo "\n\t- rule construct";
$test->ruleConstruct();
echo " - OK";

echo "\n\t- create with rules";
$test->createWithRules();
echo " - OK";

echo "\n\t- debtor workspace id";
$test->debtorWorkspaceId();
echo " - OK";
