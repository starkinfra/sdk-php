<?php

namespace StarkInfra;

use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Rest;

class PixDomain extends Resource
{
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> certificates = Checks::checkParam($params, "certificates");
        $this-> name = Checks::checkParam($params, "name");

        Checks::checkParams($params);
    }

    /**
    # Retrieve PixDomains
    
    Receive a generator of PixDomain objects.
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - generator of PixDomain objects with updated attributes
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
