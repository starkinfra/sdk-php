<?php

namespace Test\Utils;
use \Exception;
use StarkInfra\CardMethod;
use StarkInfra\IssuingRule;
use StarkInfra\MerchantCountry;
use StarkInfra\MerchantCategory;


class Rule
{
    public static function generateExampleRulesJson($n=1)
    {
        $rules = [];

        $intervals = ["day", "week", "month", "instant"];
        $currencies = ["BRL", "USD"];
    
        foreach (range(1, $n) as $index) {
            $rule = new IssuingRule([
                "name" => "Example Rule",
                "interval" => $intervals[array_rand($intervals)],
                "amount" => random_int(1000, 100000),
                "currencyCode" => $currencies[array_rand($currencies)],
                "categories" => [new MerchantCategory([
                    "code" => "veterinaryServices"
                ])],
                "countries" => [new MerchantCountry([
                    "code" => "BRA"
                ])],
                "methods" => [new CardMethod([
                    "code" => "token"
                ])],
            ]);
            array_push($rules, $rule);
        }
        return $rules;
    }
}
