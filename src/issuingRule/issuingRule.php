<?php

namespace StarkInfra;
use StarkCore\Utils\API;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class IssuingRule extends Resource
{

    public $name;
    public $interval;
    public $amount;
    public $currencyCode;
    public $counterAmount;
    public $currencyName;
    public $currencySymbol;
    public $categories;
    public $countries;
    public $methods;

    /**
    # IssuingRule object

    The IssuingRule object displays the spending rules of IssuingCards and IssuingHolders created in your Workspace.

    ## Parameters (required):
        - name [string]: rule name. ex: "Travel" or "Food"
        - amount [string]: maximum amount that can be spent in the informed interval. ex: 200000 (= R$ 2000.00)
        
    ## Parameters (optional):
        - id [string, default null]: unique id returned when Rule is created. ex: "5656565656565656"
        - interval [string, default "lifetime"]: interval to reset the counters of the rule. ex: "instant", "day", "week", "month", "year" or "lifetime"
        - currencyCode [string, default "BRL"]: code of the currency used by the rule. ex: "BRL" or "USD"
        - categories [array of MerchantCategories objects, default []]: merchant categories accepted by the rule. ex: ["eatingPlacesRestaurants", "travelAgenciesTourOperators"]
        - countries [array of MerchantCountries objects, default []]: countries accepted by the rule. ex: ["BRA", "USA"]
        - methods [array of CardMethods objects, default []]: card purchase methods accepted by the rule. ex: ["contactless", "token"]

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
        $this->categories = MerchantCategory::parseCategories(Checks::checkParam($params, "categories"));
        $this->countries = MerchantCountry::parseCountries(Checks::checkParam($params, "countries"));
        $this->methods = CardMethod::parseMethods(Checks::checkParam($params, "methods"));

        Checks::checkParams($params);
    }

    public static function parseRules($rules) {
        if ($rules == null) {
            return null;
        }
        $parsedRules = [];
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
