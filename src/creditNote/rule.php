<?php

namespace StarkInfra\CreditNote;
use StarkCore\Utils\API;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class Rule extends SubResource
{

    public $key;
    public $value;

    /**
    # CreditNote\Rule object

    The CreditNote\Rule object modifies the behavior of CreditNotes when passed
    as an argument upon their creation.

    ## Parameters (required):
        - key [string]: Rule to be customized, describes what CreditNote behavior will be altered. ex: "invoiceCreationMode"
        - value [string]: Value of the rule. ex: "scheduled", "instant" or "never"
    */
    function __construct(array $params)
    {
        $this-> key = Checks::checkParam($params, "key");
        $this-> value = Checks::checkParam($params, "value");

        Checks::checkParams($params);
    }
}
