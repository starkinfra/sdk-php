<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class CreditSigner extends Resource
{

    public $name;
    public $contact;
    public $method;

    /**
    # CreditSigner object

    CreditNote signer's information.

    ## Parameters (required):
        - name [string]: signer's name. ex: "Tony Stark"
        - contact [string]: signer's contact information. ex: "tony@starkindustries.com"
        - method [string]: delivery method for the contract. Options: "link" (signing link sent to contact), "token" (signing token sent to contact), "server"/"organization" (automatic signature over a URL contact). ex: "link"

    Attributes (return-only):
        - id [string]: unique id returned when the CreditSigner is created. ex: "5656565656565656"
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->name = Checks::checkParam($params, "name");
        $this->contact = Checks::checkParam($params, "contact");
        $this->method = Checks::CheckParam($params, "method");

        Checks::checkParams($params);
    }

    /**
    # Resend token to signer

    Resend the contract signing token to a specific CreditSigner.

    ## Parameters (required):
        - signerId [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - CreditSigner object with updated attributes
     */
    public static function resendToken($signerId, $user = null)
    {
        return Rest::patchId($user, CreditSigner::resource(), $signerId, ["isSent" => false]);
    }

    private static function resource()
    {
        $signer = function ($array) {
            return new CreditSigner($array);
        };
        return [
            "name" => "CreditSigner",
            "maker" => $signer,
        ];
    }
}
