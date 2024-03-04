<?php

namespace Test\IssuingToken;
use \Exception;
use StarkInfra\IssuingToken;
use StarkCore\Error\InvalidSignatureError;


class TestIssuingToken
{
    const CONTENT = "{\"deviceName\": \"My phone\", \"methodCode\": \"manual\", \"walletName\": \"Google Pay\", \"activationCode\": \"\", \"deviceSerialNumber\": \"2F6D63\", \"deviceImei\": \"352099001761481\", \"deviceType\": \"Phone\", \"walletInstanceId\": \"1b24f24a24ba98e27d43e345b532a245e4723d7a9c4f624e\", \"deviceOsVersion\": \"4.4.4\", \"cardId\": \"5189831499972623\", \"deviceOsName\": \"Android\", \"merchantId\": \"12345678901\", \"walletId\": \"google\"}";
    const VALID_SIGNATURE = "MEYCIQC4XbhjxEp9VhowLeg9JbSOo94FCRWE9GI7l7OuHh0bUwIhAJBuLDl5DAT9L4iMI0qYQ+PVmBIG5scxxvkWSsoWmwi4";
    const INVALID_SIGNATURE = "MEUCIQDOpo1j+V40DNZK2URL2786UQK/8mDXon9ayEd8U0/l7AIgYXtIZJBTs8zCRR3vmted6Ehz/qfw1GRut/eYyvf1yOk=";

    public function queryAndGet()
    {
        $tokens = IssuingToken::query(["limit" => 5]);

        foreach ($tokens as $token) {

            if (is_null($token->id)) {
                throw new Exception("failed");
            }

            $token = iterator_to_array(IssuingToken::query(["limit" => 1]))[0];
            $token = IssuingToken::get($token->id);

            if (!is_string($token->id)) {
                throw new Exception("failed");
            }
        }        
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) { 
            list($page, $cursor) = IssuingToken::page($options = ["limit" => 2, "cursor" => $cursor]);
            foreach ($page as $issuingToken) {
                if (in_array($issuingToken->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $issuingToken->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 4) {
            throw new Exception("failed");
        }
    }

    public function queryAndUpdate()
    {
        $tokens = IssuingToken::query(["status" => "active", "limit" => 1]);
        foreach ($tokens as $token) {
            if (is_null($token->id)) {
                throw new Exception("failed");
            }
            $updatedtoken = IssuingToken::update($token->id, ["status" => "blocked"]);
            if ($updatedtoken->result != "blocked") {
                throw new Exception("failed");
            }    
        }
    }

    public function queryAndDelete()
    {
        $tokens = iterator_to_array(IssuingToken::query(["limit"=>1]));

        if (count($tokens) != 1){
            throw new Exception("failed");
        }
        $token = IssuingToken::cancel($tokens[0]->id);
        if ($tokens[0]->status == "canceled") {
            throw new Exception("failed");
        } 
        if ($tokens[0]->id != $token->id) {
            throw new Exception("failed");
        } 
    }

    public function parseRight()
    {
        $token_1 = IssuingToken::parse(self::CONTENT, self::VALID_SIGNATURE);
        $token_2 = IssuingToken::parse(self::CONTENT, self::VALID_SIGNATURE); // using cache

        if ($token_1 != $token_2) {
            throw new Exception("failed");
        }
    }

    public function parseWrong()
    {
        $error = false;
        try {
            $token = IssuingToken::parse(self::CONTENT, self::INVALID_SIGNATURE);
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
            $token = IssuingToken::parse(self::CONTENT, "something is definitely wrong");
        } catch (InvalidSignatureError $e) {
            $error = true;
        }

        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function createResponseAuthorization()
    {
        $response = IssuingToken::responseAuthorization(["status"=>"approved", "amount"=>1000]);
        if (gettype($response) != "string") {
            throw new Exception("failed");
        }
        if (strlen($response) == 0) {
            throw new Exception("failed");
        }
    }

    public function createResponseActivation()
    {
        $response = IssuingToken::responseActivation(["status"=>"accepted", "amount"=>1000]);
        if (gettype($response) != "string") {
            throw new Exception("failed");
        }
        if (strlen($response) == 0) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nIssuingToken:";

$test = new TestIssuingToken();

// echo "\n\t- query and get";
// $test->queryAndGet();
// echo " - OK";

// echo "\n\t- query and update";
// $test->queryAndUpdate();
// echo " - OK";

echo "\n\t- parse right";
$test->parseRight();
echo " - OK";

// echo "\n\t- parse wrong";
// $test->parseWrong();
// echo " - OK";

// echo "\n\t- parse malformed";
// $test->parseMalformed();
// echo " - OK";

// echo "\n\t- create response activation";
// $test->createResponseActivation();
// echo " - OK";

// echo "\n\t- create response authorization";
// $test->createResponseAuthorization();
// echo " - OK";
