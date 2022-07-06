<?php

namespace Test\Webhook;

use StarkInfra\Webhook;
use \Exception;

class TestWebhook
{
    public function createAndDelete()
    {
        $webhoook = Webhook::create(TestWebhook::example());

        if (is_null($webhoook->id)) {
            throw new Exception("failed");
        }

        $webhoookCan = Webhook::delete($webhoook->id);

        if (is_null($webhoookCan->id)) {
            throw new Exception("failed");
        }
    } 

    public function queryAndGet()
    {
        $webhooks = iterator_to_array(Webhook::query(["limit" => 1]));
        
        if (count($webhooks) != 1) {
            throw new Exception("failed");
        }

        $webhook = Webhook::get($webhooks[0]->id);

        if ($webhooks[0]->id != $webhook->id) {
            throw new Exception("failed");
        }
    }

    public function getPage()
    {
        $ids = [];
        $cursor = null;
        for ($i=0; $i < 3; $i++) { 
            list($page, $cursor) = Webhook::page($options = ["limit" => 1, "cursor" => $cursor]);
            foreach ($page as $webhoook) {
                if (in_array($webhoook->id, $ids)) {
                    throw new Exception("failed");
                }
                array_push($ids, $webhoook->id);
            }
            if ($cursor == null) {
                break;
            }
        }
        if (count($ids) != 3) {
            throw new Exception("failed");
        }
    }

    public function example()
    {
        $params = [
            "url" => "https://webhook.site/",
            "subscriptions" =>[
                "credit-note", "signer",
                "issuing-card", "issuing-invoice", "issuing-purchase",
                "pix-request.in", "pix-request.out", "pix-reversal.in", "pix-reversal.out", "pix-claim", "pix-key", "pix-infraction", "pix-chargeback"
            ]
        ];
        return new Webhook($params);
    }
}

echo "\nWebhook:";

$test = new TestWebhook();

echo "\n\t- create and delete";
$test->createAndDelete();
echo " - OK";

echo "\n\t- query and get";
$test->queryAndGet();
echo " - OK";

echo "\n\t- get page";
$test->getPage();
echo " - OK";
