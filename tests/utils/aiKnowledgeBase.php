<?php

namespace Test\Utils;
use StarkInfra\AiKnowledgeBase;


class KnowledgeBaseExample
{
    public static function generateExampleAiKnowledgeBase()
    {
        return new AiKnowledgeBase([
            "name" => "sdk-php-" . bin2hex(random_bytes(6)),
            "rootUrl" => "https://docs.starkinfra.com",
            "isRecursive" => false,
            "tags" => ["sdk-php", "test"]
        ]);
    }
}
