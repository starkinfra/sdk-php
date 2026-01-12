<?php

namespace Test\PixDispute;
use \Exception;
use StarkInfra\Event;
use StarkInfra\PixDispute;
use StarkCore\Error\InputErrors;
use StarkCore\Error\InvalidSignatureError;


class TestPixDispute
{
    public function queryAndGet()
    {
        $disputes = iterator_to_array(PixDispute::query(["limit" => 10]));

        if (count($disputes) != 10) {
            throw new Exception("failed");
        }

        $dispute = PixDispute::get($disputes[0]->id);

        if ($disputes[0]->id != $dispute->id) {
            throw new Exception("failed");
        }
    }

    public function queryParams()
    {
        $disputes = iterator_to_array(PixDispute::query([
            "limit" => 10,
            "after" => "2020-04-01",
            "before" => "2020-04-30",
            "status" => ['closed','failed'],
            "tags" => ['iron', 'suit'],
            "ids" => ['1', '2'],
        ]));

        if (count($disputes) != 0) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 2; $i++) {
            list($page, $cursor) = PixDispute::page($options = ["limit" => 5, "cursor" => $cursor]);
            foreach ($page as $dispute) {
                if (in_array($dispute->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $dispute->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 10) {
            throw new Exception("failed");
        }
    }

    public function cancel()
    {
        $options = [
            "status" => ["created", "delivered"],
            "limit" => 1
        ];
        $disputes = iterator_to_array(PixDispute::query($options));

        if (count($disputes) == 0) {
            throw new Exception("no disputes to be cancelled found");
        }

        $dispute = $disputes[0];

        try {
            $canceledDispute = PixDispute::cancel($dispute->id);
        } catch (Exception $e) {
            throw new Exception("failed");
        }

        if($canceledDispute->id != $dispute->id) {
            throw new Exception("failed");
        }
    }

    public function parsePixDisputeEventLog()
    {
        $content = '{"event": {"created": "2025-12-19T19:20:08.687079+00:00", "id": "4543235613523968", "log": {"created": "2025-12-19T19:20:08.107566+00:00", "dispute": {"bacenId": "42e3c802-22c0-4862-b352-cedc912c07a1", "created": "2025-12-19T19:16:04.867430+00:00", "description": "", "flow": "in", "id": "4652621482688512", "maxHopCount": 5, "maxHopInterval": 86400, "maxTransactionCount": 500, "method": "scam", "minTransactionAmount": 20000, "operatorEmail": "fraud@company.com", "operatorPhone": "+5511989898989", "referenceId": "E20018183202512191914WcfANNEIYnt", "status": "analysed", "tags": [], "transactions": [{"amount": 20000, "endToEndId": "E20018183202512191914WcfANNEIYnt", "nominalAmount": 20000, "receiverAccountCreated": "", "receiverBankCode": "39908427", "receiverId": "1", "receiverTaxIdCreated": "", "receiverType": "business", "senderAccountCreated": "", "senderBankCode": "20018183", "senderId": "2", "senderTaxIdCreated": "", "senderType": "business", "settled": "2025-12-19T19:14:25.760000+00:00"}], "updated": "2025-12-19T19:20:08.107585+00:00"}, "errors": [], "id": "6007878011846656", "type": "analysed"}, "subscription": "pix-dispute", "workspaceId": "5560467233701888"}}';
        $validSignature = "MEYCIQCPgzyktxttTM9ooQaXq37NvFjL2cF/nQMfl1rvUcsLAQIhAKLbphPa5311mHvXlz6Rtkk+LPhctxgGYOnxAdhdldls";
        
        try {
            $event = Event::parse($content, $validSignature);
        } catch (InvalidSignatureError $e) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nPixDispute:";

$test = new TestPixDispute();

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- query with params";
$test->queryParams();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";

echo "\n\t- cancel";
$test->cancel();
echo " - OK";

echo "\n\t- parse pix dispute event log";
$test->parsePixDisputeEventLog();
echo " - OK";
