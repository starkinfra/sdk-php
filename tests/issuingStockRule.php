<?php

namespace Test\IssuingStockRule;
use \Exception;
use StarkInfra\IssuingStock;
use StarkInfra\IssuingStockRule;


class TestIssuingStockRule
{
    public function query()
    {
        $rules = IssuingStockRule::query(["limit" => 10, "after" => "2020-01-01", "before" => "2030-01-01"]);

        foreach ($rules as $rule) {
            if (!is_string($rule->id)) {
                throw new Exception("failed");
            }
        }
    }

    public function page()
    {
        list($rules, $cursor) = IssuingStockRule::page(["limit" => 3]);

        foreach ($rules as $rule) {
            if (is_null($rule->id)) {
                throw new Exception("failed");
            }
        }
        if (is_null($cursor)) {
            throw new Exception("failed");
        }
    }

    public function postPatchAndCancel()
    {
        $stock = iterator_to_array(IssuingStock::query(["limit" => 1]))[0];

        $activeRules = IssuingStockRule::query([
            "stockIds" => [$stock->id],
            "status" => ["active"],
            "after" => "2020-01-01",
            "before" => "2030-01-01"
        ]);
        foreach ($activeRules as $activeRule) {
            IssuingStockRule::cancel($activeRule->id);
        }

        $rule = IssuingStockRule::create([TestIssuingStockRule::example($stock->id)])[0];
        if (is_null($rule->id) || $rule->id == "") {
            throw new Exception("failed");
        }

        $rule = IssuingStockRule::update($rule->id, ["minimumBalance" => 20000]);
        if ($rule->minimumBalance != 20000) {
            throw new Exception("failed");
        }

        $rule = IssuingStockRule::cancel($rule->id);
        if ($rule->status != "canceled") {
            throw new Exception("failed");
        }
    }

    public static function example($stockId)
    {
        $params = [
            "minimumBalance" => 1000,
            "stockId" => $stockId,
            "emails" => ["john.doe@enterprise.com"],
            "phones" => ["+55 (11) 91234 5678"]
        ];
        return new IssuingStockRule($params);
    }
}

echo "\n\nIssuingStockRule:";

$test = new TestIssuingStockRule();

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- page";
$test->page();
echo " - OK";

echo "\n\t- post, patch and cancel";
$test->postPatchAndCancel();
echo " - OK";
