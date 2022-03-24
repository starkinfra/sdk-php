<?php

namespace Test\Event;

use \Exception;
use StarkInfra\Event;
use StarkInfra\Error\InvalidSignatureError;


class TestEvent
{

    const CONTENT = '{"event": {"created": "2022-02-15T20:45:09.852878+00:00", "id": "5015597159022592", "log": {"created": "2022-02-15T20:45:09.436621+00:00", "errors": [{"code": "insufficientFunds", "message": "Amount of funds available is not sufficient to cover the specified transfer"}], "id": "5288053467774976", "request": {"amount": 1000, "bankCode": "34052649", "cashAmount": 0, "cashierBankCode": "", "cashierType": "", "created": "2022-02-15T20:45:08.210009+00:00", "description": "For saving my life", "endToEndId": "E34052649202201272111u34srod1a91", "externalId": "141322efdgber1ecd1s342341321", "fee": 0, "flow": "out", "id": "5137269514043392", "initiatorTaxId": "", "method": "manual", "receiverAccountNumber": "000001", "receiverAccountType": "checking", "receiverBankCode": "00000001", "receiverBranchCode": "0001", "receiverKeyId": "", "receiverName": "Jamie Lennister", "receiverTaxId": "45.987.245/0001-92", "reconciliationId": "", "senderAccountNumber": "000000", "senderAccountType": "checking", "senderBankCode": "34052649", "senderBranchCode": "0000", "senderName": "tyrion Lennister", "senderTaxId": "012.345.678-90", "status": "failed", "tags": [], "updated": "2022-02-15T20:45:09.436661+00:00"}, "type": "failed"}, "subscription": "pix-request.out", "workspaceId": "5692908409716736"}}';
    const VALID_SIGNATURE = "MEYCIQD0oFxFQX0fI6B7oqjwLhkRhkDjrOiD86wguEKWdzkJbgIhAPNGUUdlNpYBe+npOaHa9WJopzy3WJYl8XJG6f4ek2R/";
    const INVALID_SIGNATURE = "MEYCIQD0oFxFQX0fI6B7oqjwLhkRhkDjrOiD86wjjEKWdzkJbgIhAPNGUUdlNpYBe+npOaHa9WJopzy3WJYl8XJG6f4ek2R/";

    public function parseRight()
    {
        $event_1 = Event::parse(self::CONTENT, self::VALID_SIGNATURE);
        $event_2 = Event::parse(self::CONTENT, self::VALID_SIGNATURE); // using cache

        if ($event_1 != $event_2) {
            throw new Exception("failed");
        }
        if ($event_1->id == null) {
            throw new Exception("failed");
        }
    }

    public function parseWrong()
    {
        $error = false;
        try {
            $event = Event::parse(self::CONTENT, self::INVALID_SIGNATURE);
        } catch (InvalidSignatureError $e) {
            $error = true;
        }

        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function parseMalformed()
    {
        $error = false;
        try {
            $event = Event::parse(self::CONTENT, "something is definitely wrong");
        } catch (InvalidSignatureError $e) {
            $error = true;
        }

        if (!$error) {
            throw new Exception("failed");
        }
    }


    const CONTENT_issuing = '{"event": {"created": "2022-03-23T14:47:02.208326+00:00", "id": "6208344364679168", "log": {"created": "2022-03-23T14:46:58.346182+00:00", "id": "6035120951656448", "invoice": {"amount": 400000, "created": "2022-03-23T14:46:57.899781+00:00", "id": "4909221044813824", "issuingTransactionId": null, "status": "created", "tags": ["war supply", "invoice #1234"], "taxId": "20.018.183/0001-80", "updated": "2022-03-23T14:47:01.785490+00:00"}, "type": "created"}, "subscription": "issuing-invoice", "workspaceId": "5692908409716736"}}';
    const VALID_SIGNATURE_issuing = "MEYCIQDqDvei722n330BU1FqdBJI3yMNtW3yF0YZuyBRDpVpFQIhAPeTBbT7oDyicbU8lkFy0sLTj8FjU0/E+z+zDvYdPF/g";

    public function parseRight_issuing()
    {
        $event_1 = Event::parse(self::CONTENT_issuing, self::VALID_SIGNATURE_issuing);
        $event_2 = Event::parse(self::CONTENT_issuing, self::VALID_SIGNATURE_issuing); // using cache

        if ($event_1 != $event_2) {
            throw new Exception("failed");
        }
        if ($event_1->id == null) {
            throw new Exception("failed");
        }
    }

    public function parseWrong_issuing()
    {
        $error = false;
        try {
            $event = Event::parse(self::CONTENT_issuing, self::INVALID_SIGNATURE);
        } catch (InvalidSignatureError $e) {
            $error = true;
        }

        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function parseMalformed_issuing()
    {
        $error = false;
        try {
            $event = Event::parse(self::CONTENT_issuing, "something is definitely wrong");
        } catch (InvalidSignatureError $e) {
            $error = true;
        }

        if (!$error) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nEvent:";

$test = new TestEvent();

echo "\n\t- parse right";
$test->parseRight();
echo " - OK";

echo "\n\t- parse wrong";
$test->parseWrong();
echo " - OK";

echo "\n\t- parse malformed";
$test->parseMalformed();
echo " - OK";

echo "\n\t- parse right";
$test->parseRight_issuing();
echo " - OK";

echo "\n\t- parse wrong";
$test->parseWrong_issuing();
echo " - OK";

echo "\n\t- parse malformed";
$test->parseMalformed_issuing();
echo " - OK";