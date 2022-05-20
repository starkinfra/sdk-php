<?php

namespace StarkInfra;

use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;

class Certificate extends Resource
{
    /**
    # PixDomain\Certificate object
    The Certificate object displays the certificate information from a specific domain.
    
    ## Attributes (return-only):
        - content [string]: certificate of the Pix participant in PEM format.
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> content = Checks::checkParam($params, "content");
    }
}
