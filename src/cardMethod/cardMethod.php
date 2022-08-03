<?php

namespace StarkInfra;
use StarkInfra\Utils\API;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\SubResource;


class CardMethod extends SubResource
{
    /**
    # CardMethod object

    CardMethod's codes are used to define methods filters in IssuingRules.

    ## Parameters (required):
        - code [string]: method's code. Options: "chip", "token", "server", "manual", "contactless"

    ## Attributes (return-only):
        - name [string]: method's name. ex: "token"
        - number [string]: method's number. ex: "81"
    */
    function __construct(array $params)
    {
        $this-> code = Checks::checkParam($params, "code");
        $this-> name = Checks::checkParam($params, "name");
        $this-> number = Checks::checkParam($params, "number");

        Checks::checkParams($params);
    }

    /**
    # Retrieve CardMethods

    Receive an enumerator of CardMethod objects previously created in the Stark Infra API

    ## Parameters (optional):
        - search [string, default null]: keyword to search for code, type, name or number. ex: "token"
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of CardMethod objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        return Rest::getList($user, CardMethod::resource(), $options);
    }

    public static function parseMethods($methods) {
        if (is_null($methods)){
            return [];
        }
        $parsedMethods = [];
        foreach($methods as $method) {
            if($method instanceof CardMethod) {
                array_push($parsedMethods, $method);
                continue;
            }
            $parsedMethod = function ($array) {
                $methodMaker = function ($array) {
                    return new CardMethod($array);
                };
                return API::fromApiJson($methodMaker, $array);
            };
            array_push($parsedMethods, $parsedMethod($method));
        }    
        return $parsedMethods;
    }

    private static function resource()
    {
        $method = function ($array) {
            return new CardMethod($array);
        };
        return [
            "name" => "CardMethod",
            "maker" => $method,
        ];
    }
}
