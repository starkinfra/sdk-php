<?php

namespace StarkInfra\CreditNote\Invoice;
use StarkInfra\Utils\SubResource;
use StarkInfra\Utils\Checks;

class Discount extends SubResource
{
    /**
    # CreditNote\Invoice\Discount object
    
    Used to define a discount in the Invoice.
    
    ## Parameters (required):
        - percentage [integer]: discount percentage that will be applied. ex: 2.5
        - due [string]: Date after when the discount will be overdue in UTC ISO format. ex: "2020-11-25T17:59:26.249976+00:00"
    */
    function __construct(array $params)
    {
        $this-> percentage = Checks::checkParam($params, "percentage");
        $this-> due = Checks::checkParam($params, "due");
    }
}
