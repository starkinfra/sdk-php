<?php

namespace Test\PixInternalTransactionReport;
use \Exception;
use StarkInfra\PixInternalTransactionReport;
use StarkInfra\Utils\EndToEndId;
use StarkInfra\Utils\ReturnId;


class TestPixInternalTransactionReport
{
    public function create()
    {
        $report = PixInternalTransactionReport::create([TestPixInternalTransactionReport::example(false)])[0];

        if (is_null($report->id)) {
            throw new Exception("failed");
        }
    }

    public function createReversal()
    {
        $report = PixInternalTransactionReport::create([TestPixInternalTransactionReport::example(true)])[0];

        if (is_null($report->id)) {
            throw new Exception("failed");
        }
    }

    public function queryAndGet()
    {
        $reports = iterator_to_array(PixInternalTransactionReport::query(["limit" => 10]));

        if (count($reports) != 10) {
            throw new Exception("failed");
        }

        $report = PixInternalTransactionReport::get($reports[0]->id);

        if ($reports[0]->id != $report->id) {
            throw new Exception("failed");
        }
    }

    public function queryParams()
    {
        $reports = iterator_to_array(PixInternalTransactionReport::query([
            "limit" => 10,
            "after" => "2020-04-01",
            "before" => "2020-04-30",
            "status" => ['success', 'failed'],
            "ids" => ['1', '2'],
        ]));

        if (count($reports) != 0) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        list($page, $cursor) = PixInternalTransactionReport::page($options = ["limit" => 5]);

        foreach ($page as $report) {
            if (is_null($report->id)) {
                throw new Exception("failed");
            }
        }

        if (is_null($cursor)) {
            throw new Exception("failed");
        }
    }

    public static function example($reversal = false)
    {
        $params = [
            "amount" => 1234,
            "created" => "2026-06-16T17:23:53.980238+00:00",
            "endToEndId" => EndToEndId::create($_SERVER["SANDBOX_BANK_CODE"]),
            "method" => "manual",
            "referenceType" => $reversal ? "reversal" : "request",
            "senderAccountNumber" => "76543-8",
            "senderBranchCode" => "2201",
            "senderAccountType" => "checking",
            "senderBankCode" => $_SERVER["SANDBOX_BANK_CODE"],
            "senderTaxId" => "594.739.480-42",
            "receiverAccountNumber" => "00000-1",
            "receiverBranchCode" => "0001",
            "receiverAccountType" => "checking",
            "receiverBankCode" => "18236120",
            "receiverTaxId" => "01234567890",
            "receiverKeyId" => "+5511989898989",
        ];
        if ($reversal) {
            $params["returnId"] = ReturnId::create($_SERVER["SANDBOX_BANK_CODE"]);
        }
        return new PixInternalTransactionReport($params);
    }
}

echo "\n\nPixInternalTransactionReport:";

$test = new TestPixInternalTransactionReport();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- create reversal";
$test->createReversal();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- queryParams";
$test->queryParams();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
