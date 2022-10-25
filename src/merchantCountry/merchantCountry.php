<?php

namespace StarkInfra;
use StarkCore\Utils\API;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class MerchantCountry extends SubResource
{
    /**
    # MerchantCountry object

    MerchantCountry's codes are used to define country filters in IssuingRules.

    ## Parameters (required):
        - code [string]: country's code. ex: "BRA"

    ## Attributes (return-only):
        - name [string]: country's name. ex: "Brazil"
        - number [string]: country's number. ex: "076"
        - shortCode [string]: country's short code. ex: "BR"
    */
    function __construct(array $params)
    {
        $this-> code = Checks::checkParam($params, "code");
        $this-> name = Checks::checkParam($params, "name");
        $this-> number = Checks::checkParam($params, "number");
        $this-> shortCode = Checks::checkParam($params, "shortCode");

        Checks::checkParams($params);
    }

    /**
    # Retrieve MerchantCategories

    Receive an enumerator of MerchantCountry objects previously created in the Stark Infra API

    ## Parameters (optional):
        - search [string, default null]: keyword to search for code, type, name or number. ex: "Brazil"
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of MerchantCountry objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        return Rest::getList($user, MerchantCountry::resource(), $options);
    }

    public static function parseCountries($countries) {
        if (is_null($countries)){
            return [];
        }
        $parsedCountries = [];
        foreach($countries as $country) {
            if($country instanceof MerchantCountry) {
                array_push($parsedCountries, $country);
                continue;
            }
            $parsedCountry = function ($array) {
                $countryMaker = function ($array) {
                    return new MerchantCountry($array);
                };
                return API::fromApiJson($countryMaker, $array);
            };
            array_push($parsedCountries, $parsedCountry($country));
        }    
        return $parsedCountries;
    }

    private static function resource()
    {
        $country = function ($array) {
            return new MerchantCountry($array);
        };
        return [
            "name" => "MerchantCountry",
            "maker" => $country,
        ];
    }
}
