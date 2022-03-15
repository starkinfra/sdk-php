<?php

namespace StarkInfra;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;


class IssuingRule extends Resource
{
    /**
    # IssuingRule object

    Displays the IssuingRule objects created to your Workspace.

    ## Parameters (required):
        - name [string]: rule name. ex: "Travel" or "Food"
        - amount [string]: amount to be used in informed interval. ex: 200000 (= R$ 2000.00)
        - interval [string]: interval to reset the counters of the rule. ex: "instant", "day", "week", "month", "year" or "lifetime"

    ## Attributes (return-only):
        - id [string, default null]: unique id returned when Rule is created. ex: "5656565656565656"
        - currencyCode [string, default null]: code of the currency used by the rule. ex: "BRL" or "USD"

    ## Attributes (expanded return-only):
        - counterAmount [integer, default null]: amount spent per rule. ex: 200000 (= R$ 2000.00)
        - currencyName [string, default null]: currency name. ex: "Brazilian Real"
        - currencySymbol [string, default null]: currency symbol. ex: "R$"
        - categories [list of strings, default []]: merchant categories accepted by the rule. ex: ["eatingPlacesRestaurants", "travelAgenciesTourOperators"]
        - countries [list of strings, default []]: countries accepted by the rule. ex: ["BRA", "USA"]
        - methods [list of strings, default []]: methods accepted by the rule. ex: ["contactless", "token"]
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
}
