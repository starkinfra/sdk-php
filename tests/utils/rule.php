<?php

namespace Test\Utils;

use \Exception;
use StarkInfra\IssuingRule;


class Rule
{

    public static function generateExampleRulesJson($n=1)
    {
        $rules = [];

        $intervals = array("day", "week", "month", "instant");
        $currencies = array("BRL", "USD");
    
        foreach (range(1, $n) as $index) {
            $rule = new IssuingRule([
                "name" => "Example Rule",
                "interval" => $intervals[array_rand($intervals)],
                "amount" => random_int(1000, 100000),
                "currencyCode" => $currencies[array_rand($currencies)]
            ]);
            array_push($rules, $rule);
        }
        return $rules;
    }
}
