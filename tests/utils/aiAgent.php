<?php

namespace Test\Utils;
use StarkInfra\AiAgent;


class AgentExample
{
    public static function generateExampleAiAgent($knowledgeBaseIds = null)
    {
        $params = [
            "name" => "sdk-php-agent-" . bin2hex(random_bytes(6)),
            "model" => "bender-1.0",
            "systemPrompt" => "Answer in one short sentence.",
            "metadataSchema" => ["order_id" => ["type" => "string", "description" => "Order the customer mentions"]]
        ];
        if (!is_null($knowledgeBaseIds)) {
            $params["knowledgeBaseIds"] = $knowledgeBaseIds;
        }
        return new AiAgent($params);
    }
}
