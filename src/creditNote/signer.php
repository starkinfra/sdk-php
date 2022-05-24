<?php

namespace StarkInfra\CreditNote;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;

class Signer extends Resource
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
        parent::__construct($params);

        $this-> name = Checks::checkParam($params, "name");
        $this-> contact = Checks::checkParam($params, "contact");
        $this-> method = Checks::checkParam($params, "method");
    }
}
