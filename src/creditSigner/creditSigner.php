<?php

namespace StarkInfra;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class CreditSigner extends Resource
{
    /**
    # CreditSigner object

    CreditNote signer's information.

    ## Parameters (required):
        - name [string]: signer's name. ex: "Tony Stark"
        - contact [string]: signer's contact information. ex: "tony@starkindustries.com"
        - method [string]: delivery method for the contract. ex: "link"

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
