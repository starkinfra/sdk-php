<?php

namespace StarkInfra\CreditNote\Invoice;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class Discount extends SubResource
{

    public $percentage;
    public $due;

    /**
    # CreditNote\Invoice\Discount object
    
    Invoice discount information.
    
    ## Parameters (required):
        - percentage [float]: percentage of discount applied until specified due date. ex: 2.5
        - due [string]: due datetime for the discount in UTC ISO format. ex: "2020-11-25T17:59:26.249976+00:00"
    */
    function __construct(array $params)
    {
        $this-> percentage = Checks::checkParam($params, "percentage");
        $this-> due = Checks::checkParam($params, "due");

        Checks::checkParams($params);
    }
}
