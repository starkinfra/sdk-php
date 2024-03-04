<?php

namespace StarkInfra;
use StarkInfra\Utils\Parse;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class IssuingTokenActivation extends SubResource
{

    public $cardId;
    public $tokenId;
    public $tags;
    public $activationMethod;

    /**
    # issuingTokenActivation object

    The IssuingTokenActivation object displays the necessary information to proceed with the card tokenization.
    You will receive this object at your registered URL to notify you which method your user want to receive the activation code.
    The POST request must be answered with no content, within 2 seconds, and with an HTTP status code 200.
    After that, you may generate the activation code and send it to the cardholder.

    ## Attributes (return-only):
        - cardId [string]: card ID which the token is bounded to. ex: "5656565656565656"
        - tokenId [string]: token unique id. ex: "5656565656565656" 
        - tags [list of strings]: tags to filter retrieved object. ex: ["tony", "stark"]
        - activationMethod [dictionary]: dictionary object with "type":string and "value":string pairs
    */
    function __construct(array $params)
    {
        $this-> cardId = Checks::checkParam($params, "cardId");
        $this-> tokenId = Checks::checkParam($params, "tokenId");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> activationMethod = Checks::checkParam($params, "activationMethod");

        Checks::checkParams($params);
    }

    /**
    # Create a single verified IssuingToken authorization request from a content string

    Use this method to parse and verify the authenticity of the request received at the informed endpoint.
    Activation requests are posted to your registered endpoint whenever IssuingTokenActivations are received.
    If the provided digital signature does not check out with the StarkInfra public key, a stark.exception.InvalidSignatureException will be raised.

    ## Parameters (required):
        - content [string]: response content from request received at user endpoint (not parsed)
        - signature [string]: base-64 digital signature received at response header "Digital-Signature"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - Parsed IssuingToken object
     */
    public static function parse($content, $signature, $user = null)
    {
        return Parse::parseAndVerify($content, $signature, issuingTokenActivation::resource(), $user);
    }

    private static function resource()
    {
        $method = function ($array) {
            return new issuingTokenActivation($array);
        };
        return [
            "name" => "issuingTokenActivation",
            "maker" => $method,
        ];
    }
}
