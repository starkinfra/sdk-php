<?php

namespace Test\CreditNoteTemplateReplacements;
use Exception;
use DateTime;
use DateInterval;
use StarkCore\Key;
use StarkCore\Utils\API;
use StarkInfra\Project;
use StarkInfra\CreditNote;
use StarkInfra\CreditSigner;
use StarkCore\Error\UnknownError;

require_once("vendor/autoload.php");


// Only the outgoing HTTP call is replaced (an https stream wrapper records the request): the CreditNote construction,
// serialization and signing run for real. The wrapper cannot set $http_response_header, which the core reads for the
// status, so the core reports UnknownError after the request was sent; this test covers the request half only.
class RecordingHttps
{
    public static $requests = [];
    public $context;
    private $position = 0;
    private $content = '{"notes": []}';

    public function stream_open($path, $mode, $options, &$openedPath)
    {
        $http = stream_context_get_options($this->context)["http"];
        self::$requests[] = ["url" => $path, "method" => $http["method"], "body" => $http["content"] ?? null];
        return true;
    }

    public function stream_read($count)
    {
        $chunk = substr($this->content, $this->position, $count);
        $this->position += strlen($chunk);
        return $chunk;
    }

    public function stream_eof()
    {
        return $this->position >= strlen($this->content);
    }

    public function stream_stat()
    {
        return [];
    }
}


class TestCreditNoteTemplateReplacements
{
    private $project;

    public function __construct()
    {
        list($privateKeyPem, $publicKeyPem) = Key::create();
        $this->project = new Project([
            "environment" => "sandbox",
            "id" => "5656565656565656",
            "privateKey" => $privateKeyPem
        ]);
    }

    private function buildParams()
    {
        return [
            "templateId" => "0123456789101112",
            "name" => "Jamie Lannister",
            "taxId" => "012.345.678-90",
            "nominalAmount" => 100000,
            "scheduled" => (new DateTime())->add(new DateInterval("P1D"))->format("Y-m-d"),
            "payment" => new CreditNote\Transfer([
                "bankCode" => "60701190",
                "branchCode" => "7248",
                "accountNumber" => "5005482-1",
                "taxId" => "594.739.480-42",
                "name" => "Jamie Lannister"
            ]),
            "invoices" => [new CreditNote\Invoice(["amount" => 120000])],
            "signers" => [new CreditSigner([
                "contact" => "jamie.lannister@gmail.com",
                "method" => "link",
                "name" => "Jamie Lannister"
            ])],
            "externalId" => "php-offline-test",
            "streetLine1" => "Rua ABC",
            "streetLine2" => "Ap 123",
            "district" => "Jardim Paulista",
            "city" => "Sao Paulo",
            "stateCode" => "SP",
            "zipCode" => "01234-567",
        ];
    }

    private function buildNote($templateReplacements = null)
    {
        $params = $this->buildParams();
        // the core rejects an explicit null as an unknown parameter, so an absent field is an absent key
        if (!is_null($templateReplacements)) {
            $params["templateReplacements"] = $templateReplacements;
        }
        return new CreditNote($params);
    }

    private function createRecording($note)
    {
        RecordingHttps::$requests = [];
        stream_wrapper_unregister("https");
        stream_wrapper_register("https", RecordingHttps::class);
        try {
            CreditNote::create([$note], $this->project);
        } catch (UnknownError $error) {
            // expected: see the note above RecordingHttps
        } finally {
            stream_wrapper_restore("https");
        }
        if (count(RecordingHttps::$requests) != 1) {
            throw new Exception("expected exactly one request, got " . count(RecordingHttps::$requests));
        }
        return json_decode(RecordingHttps::$requests[0]["body"], true)["notes"][0];
    }

    public function testSendsTemplateReplacementsWithKeysIntact()
    {
        $replacements = [
            "guarantorName" => "Jon Snow",
            "guarantorTaxId" => "012.345.678-90",
            "guarantorAddress" => "Av. Paulista, 200 - Bela Vista - Sao Paulo, SP"
        ];
        $sent = $this->createRecording($this->buildNote($replacements));
        if (!array_key_exists("templateReplacements", $sent)) {
            throw new Exception("templateReplacements was not sent");
        }
        if ($sent["templateReplacements"] !== $replacements) {
            throw new Exception("keys or values changed: " . json_encode($sent["templateReplacements"]));
        }
    }

    public function testNestedNullValuesAreNotSent()
    {
        $sent = $this->createRecording($this->buildNote(["guarantorName" => "Jon Snow", "guarantorEmail" => null]));
        if ($sent["templateReplacements"] !== ["guarantorName" => "Jon Snow"]) {
            throw new Exception("nested null must not be sent: " . json_encode($sent["templateReplacements"]));
        }
    }

    public function testOmitsTemplateReplacementsWhenAbsent()
    {
        $sent = $this->createRecording($this->buildNote());
        if (array_key_exists("templateReplacements", $sent)) {
            throw new Exception("templateReplacements must not be sent when absent");
        }
    }

    public function testObjectAndRawArraySendTheSamePayload()
    {
        $replacements = [
            "guarantorName" => "Jon Snow",
            "guarantorTaxId" => "012.345.678-90",
            "_internalNote" => "kept as written"
        ];
        $fromArray = $this->createRecording($this->buildNote($replacements));
        $fromObject = $this->createRecording($this->buildNote(new CreditNote\TemplateReplacements($replacements)));
        if ($fromObject["templateReplacements"] !== $replacements) {
            throw new Exception("object changed keys or values: " . json_encode($fromObject["templateReplacements"]));
        }
        if ($fromObject !== $fromArray) {
            throw new Exception("object and raw array must send the same payload");
        }
    }

    public function testObjectDropsNestedNullValues()
    {
        $replacements = new CreditNote\TemplateReplacements(["guarantorName" => "Jon Snow", "guarantorEmail" => null]);
        $sent = $this->createRecording($this->buildNote($replacements));
        if ($sent["templateReplacements"] !== ["guarantorName" => "Jon Snow"]) {
            throw new Exception("nested null must not be sent: " . json_encode($sent["templateReplacements"]));
        }
    }

    public function testEmptyAndExplicitNullKeepDocumentedBehavior()
    {
        foreach ([[], new CreditNote\TemplateReplacements()] as $empty) {
            $sent = $this->createRecording($this->buildNote($empty));
            if ($sent["templateReplacements"] !== []) {
                throw new Exception("empty replacements must be sent as []: " . json_encode($sent["templateReplacements"]));
            }
        }
        try {
            new CreditNote(array_merge($this->buildParams(), ["templateReplacements" => null]));
        } catch (Exception $error) {
            if (strpos($error->getMessage(), "unknown parameters") !== false) {
                return;
            }
        }
        throw new Exception("an explicit null must keep raising unknown parameters");
    }

    public function testRawArrayBecomesTemplateReplacements()
    {
        $note = $this->buildNote(["guarantorName" => "Jon Snow"]);
        if (!($note->templateReplacements instanceof CreditNote\TemplateReplacements)) {
            throw new Exception("raw array must be converted to CreditNote\\TemplateReplacements");
        }
        $instance = new CreditNote\TemplateReplacements(["guarantorName" => "Jon Snow"]);
        if ($this->buildNote($instance)->templateReplacements !== $instance) {
            throw new Exception("an instance must be kept as given");
        }
    }

    public function testRejectsUnsupportedType()
    {
        foreach (["guarantorName=Jon", 42, new \stdClass()] as $invalid) {
            try {
                $this->buildNote($invalid);
            } catch (Exception $error) {
                continue;
            }
            throw new Exception("an unsupported type must be rejected: " . gettype($invalid));
        }
    }

    public function testKeepsEveryKeyOfTheResponse()
    {
        $response = [
            "guarantorName" => "Jon Snow",
            "guarantorTaxId" => "012.345.678-90",
            "someKeyNobodyDeclared" => "still here",
            "_underscored" => "still here too"
        ];
        $echoed = $this->createRecording($this->buildNote());
        $echoed["templateReplacements"] = $response;
        $parsed = API::fromApiJson(function ($array) { return new CreditNote($array); }, $echoed);
        $templateReplacements = $parsed->templateReplacements;
        if (!($templateReplacements instanceof CreditNote\TemplateReplacements)) {
            throw new Exception("response field must be a CreditNote\\TemplateReplacements");
        }
        if (get_object_vars($templateReplacements) !== $response) {
            throw new Exception("a response key was lost: " . json_encode(get_object_vars($templateReplacements)));
        }
        if ($templateReplacements->someKeyNobodyDeclared !== "still here") {
            throw new Exception("response key not readable as attribute");
        }
        $sent = $this->createRecording($parsed);
        if ($sent["templateReplacements"] !== $response) {
            throw new Exception("response keys must serialize back: " . json_encode($sent["templateReplacements"]));
        }
    }

    public function testAbsentResponseFieldStaysNull()
    {
        $echoed = $this->createRecording($this->buildNote());
        $parsed = API::fromApiJson(function ($array) { return new CreditNote($array); }, $echoed);
        if (!is_null($parsed->templateReplacements)) {
            throw new Exception("absent field must stay null");
        }
    }
}

$test = new TestCreditNoteTemplateReplacements();
$test->testSendsTemplateReplacementsWithKeysIntact();
$test->testNestedNullValuesAreNotSent();
$test->testOmitsTemplateReplacementsWhenAbsent();
$test->testObjectAndRawArraySendTheSamePayload();
$test->testObjectDropsNestedNullValues();
$test->testEmptyAndExplicitNullKeepDocumentedBehavior();
$test->testRawArrayBecomesTemplateReplacements();
$test->testRejectsUnsupportedType();
$test->testKeepsEveryKeyOfTheResponse();
$test->testAbsentResponseFieldStaysNull();
echo "\n\nCreditNote templateReplacements:\n\t- request payload, omission when absent, typed sub-resource and response keys - OK\n";
