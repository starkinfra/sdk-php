<?php

namespace StarkInfra\CreditNote;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class Signer extends SubResource
{
    /**
    # CreditNote\Signer object
    
    CreditNote signer's information.
    
    ## Parameters (required):
        - name [string]: signer's name. ex: "Tony Stark"
        - contact [string]: contact for the contract signature request. ex: "tony@starkindustries.com"
        - method [string]: delivery method for the contract. ex: "link"
    */
    function __construct(array $params)
    {
        $this-> name = Checks::checkParam($params, "name");
        $this-> contact = Checks::checkParam($params, "contact");
        $this-> method = Checks::checkParam($params, "method");

        Checks::checkParams($params);
    }
}
