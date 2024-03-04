<?php

namespace Test\IssuingTokenActivation;
use \Exception;
use StarkInfra\IssuingTokenActivation;
use StarkCore\Error\InvalidSignatureError;


class TestIssuingTokenActivation
{
    const CONTENT = "{\"activationMethod\": {\"type\": \"text\", \"value\": \"** *****-5678\"}, \"tokenId\": \"5585821789122165\", \"tags\": [\"token\", \"user/1234\"], \"cardId\": \"5189831499972623\"}";
    const VALID_SIGNATURE = "MEUCIAxn0FmsPWI4r3Y7Nq8xFNQHYZgo0QAGDQ4/7CajKoVuAiEA09kXWrPMhsw4JbgC3pmNccCWr+hidfop/KsSNqza0yE=";
    const INVALID_SIGNATURE = "MEUCIQDOpo1j+V40DNZK2URL2786UQK/8mDXon9ayEd8U0/l7AIgYXtIZJBTs8zCRR3vmted6Ehz/qfw1GRut/eYyvf1yOk=";

    public function parseRight()
    {
        
        $token_1 = IssuingTokenActivation::parse(self::CONTENT, self::VALID_SIGNATURE);
        $token_2 = IssuingTokenActivation::parse(self::CONTENT, self::VALID_SIGNATURE); // using cache

        if ($token_1 != $token_2) {
            throw new Exception("failed");
        }
    }

    public function parseWrong()
    {
        $error = false;
        try {
            $token = IssuingTokenActivation::parse(self::CONTENT, self::INVALID_SIGNATURE);
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
            $token = IssuingTokenActivation::parse(self::CONTENT, "something is definitely wrong");
        } catch (InvalidSignatureError $e) {
            $error = true;
        }

        if (!$error) {
            throw new Exception("failed");
        }
    }

}

echo "\n\nIssuingTokenActivation:";

$test = new TestIssuingTokenActivation();

echo "\n\t- parse right";
$test->parseRight();
echo " - OK";

echo "\n\t- parse wrong";
$test->parseWrong();
echo " - OK";

echo "\n\t- parse malformed";
$test->parseMalformed();
echo " - OK";