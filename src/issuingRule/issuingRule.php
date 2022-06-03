<?php

namespace StarkInfra;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\API;


class IssuingRule extends Resource
{
    /**
    # IssuingRule object

    Displays the IssuingRule objects created to your Workspace.

    ## Parameters (required):
        - name [string]: rule name. ex: "Travel" or "Food"
        - amount [string]: amount to be used in informed interval. ex: 200000 (= R$ 2000.00)
        
        ## Attributes (return-only):
        - id [string, default null]: unique id returned when Rule is created. ex: "5656565656565656"
        - interval [string, default "lifetime"]: interval to reset the counters of the rule. ex: "instant", "day", "week", "month", "year" or "lifetime"
        - currencyCode [string, default "BRL"]: code of the currency used by the rule. ex: "BRL" or "USD"
        - categories [array of strings, default []]: merchant categories accepted by the rule. ex: ["eatingPlacesRestaurants", "travelAgenciesTourOperators"]
        - countries [array of strings, default []]: countries accepted by the rule. ex: ["BRA", "USA"]
        - methods [array of strings, default []]: methods accepted by the rule. ex: ["contactless", "token"]

    ## Attributes (expanded return-only):
        - counterAmount [integer]: amount spent per rule. ex: 200000 (= R$ 2000.00)
        - currencySymbol [string]: currency symbol. ex: "R$"
        - currencyName [string]: currency name. ex: "Brazilian Real"
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->name = Checks::checkParam($params, "name");
        $this->interval = Checks::checkParam($params, "interval");
        $this->amount = Checks::checkParam($params, "amount");
        $this->currencyCode = Checks::checkParam($params, "currencyCode");
        $this->counterAmount = Checks::checkParam($params, "counterAmount");
        $this->currencyName = Checks::checkParam($params, "currencyName");
        $this->currencySymbol = Checks::checkParam($params, "currencySymbol");
        $this->categories = Checks::checkParam($params, "categories");
        $this->countries = Checks::checkParam($params, "countries");
        $this->methods = Checks::checkParam($params, "methods");

        Checks::checkParams($params);
    }

    public static function parseRules($rules) {
        $parsedRules = [];
        if ($rules == null) {
            return null;
        }
        foreach($rules as $rule) {
            if($rule instanceof IssuingRule) {
                array_push($parsedRules, $rule);
                continue;
            }
            $parsedRule = function ($array) {
                $ruleMaker = function ($array) {
                    return new IssuingRule($array);
                };
                return API::fromApiJson($ruleMaker, $array);
            };
            array_push($parsedRules, $parsedRule($rule));
        }    
        return $parsedRules;
    }
}
