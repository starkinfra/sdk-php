<?php

namespace StarkInfra\CreditNote\Invoice;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class Description extends SubResource
{
    /**
    # CreditNote\Invoice\Description object
    
    Used to define a description in the Invoice.
    
    ## Parameters (required):
        - key [string]: key describing a part of the invoice value. ex: "Taxes"
        - value [string]: value to which the key refers to. ex: "120"
    */
    function __construct(array $params)
    {
        $this-> key = Checks::checkParam($params, "key");
        $this-> value = Checks::checkParam($params, "value");

        Checks::checkParams($params);
    }
}
