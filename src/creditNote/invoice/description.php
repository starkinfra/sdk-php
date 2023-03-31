<?php

namespace StarkInfra\CreditNote\Invoice;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class Description extends SubResource
{

    public $key;
    public $value;

    /**
    # CreditNote\Invoice\Description object
    
    Invoice description information.
    
    ## Parameters (required):
        - key [string]: Description for the value. ex: "Taxes"

    ## Parameters (optional):
        - value [string, default null]: amount related to the described key. ex: "R$100,00"
    */
    function __construct(array $params)
    {
        $this-> key = Checks::checkParam($params, "key");
        $this-> value = Checks::checkParam($params, "value");

        Checks::checkParams($params);
    }
}
