<?php

namespace StarkInfra;
use StarkInfra\Utils\SubResource;
use StarkInfra\Utils\Checks;


class Certificate extends SubResource
{
    /**
    # PixDomain\Certificate object
    The Certificate object displays the certificate information from a specific domain.
    
    ## Attributes (return-only):
        - content [string]: certificate of the Pix participant in PEM format.
    */
    function __construct(array $params)
    {
        $this-> content = Checks::checkParam($params, "content");
    }

    public static function subResource()
    {
        $certificate = function ($array) {
            return new Certificate($array);
        };
        return [
            "name" => "Certificate",
            "maker" => $certificate,
        ];
    }
}
