<?php

namespace StarkInfra\Ledger;
use StarkCore\Utils\API;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class Rule extends SubResource
{

    public $key;
    public $value;

    /**
    # Ledger\Rule object

    The Ledger\Rule object modifies the behavior of Ledger objects when passed as an argument upon their creation or update.

    ## Parameters (required):
        - key [string]: Rule to be customized, describes what Ledger behavior will be altered. ex: "minimumBalance", "maximumBalance"
        - value [integer]: Value of the rule. ex: 1000
     */
    function __construct(array $params)
    {
        $this->key = Checks::checkParam($params, "key");
        $this->value = Checks::checkParam($params, "value");

        Checks::checkParams($params);
    }

    public static function parseRules($rules) {
        if ($rules == null) {
            return null;
        }
        $parsedRules = [];
        foreach($rules as $rule) {
            if($rule instanceof Rule) {
                array_push($parsedRules, $rule);
                continue;
            }
            $parsedRule = function ($array) {
                $ruleMaker = function ($array) {
                    return new Rule($array);
                };
                return API::fromApiJson($ruleMaker, $array);
            };
            array_push($parsedRules, $parsedRule($rule));
        }
        return $parsedRules;
    }
}
