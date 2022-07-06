<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\SubResource;


class PixDomain extends SubResource
{
    /**
    # PixDomain object
    
    The PixDomain object displays the QR Code domain certificate information of Pix participants.
    All certificates must be registered with the Central Bank.
    
    ## Attributes (return-only):
        - certificates [array of PixDomain\Certificate]: certificate information of the Pix participant.
        - name [string]: current active domain (URL) of the Pix participant.
    */
    function __construct(array $params)
    {
        $this->certificates = Checks::checkParam($params, "certificates");
        $this->name = Checks::checkParam($params, "name");

        Checks::checkParams($params);
    }

    /**
    # Retrieve PixDomains
    
    Receive an enumerator of PixDomain objects.
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - enumerator of PixDomain objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        return Rest::getList($user, PixDomain::resource(), $options);
    }

    private static function resource()
    {
        $domain = function ($array) {
            return new PixDomain($array);
        };
        return [
            "name" => "PixDomain",
            "maker" => $domain,
        ];
    } 
}
