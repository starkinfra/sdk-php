<?php

namespace Test\Utils;
use Exception;
use StarkInfra\AiAgent;
use StarkInfra\AiChat;
use StarkInfra\AiKnowledgeBase;
use StarkInfra\AiMessage;
use StarkInfra\AiSpeech;
use StarkInfra\AiVoice;

include_once(__DIR__ . "/aiKnowledgeBase.php");


class AiFixtures
{
    private static $knowledgeBase = null;
    private static $agent = null;
    private static $chat = null;
    private static $voice = null;
    private static $posted = null;
    private static $registered = false;

    public static function knowledgeBase()
    {
        if (is_null(self::$knowledgeBase)) {
            self::register();
            self::$knowledgeBase = AiKnowledgeBase::create(KnowledgeBaseExample::generateExampleAiKnowledgeBase());
        }
        return self::$knowledgeBase;
    }

    public static function agent()
    {
        if (is_null(self::$agent)) {
            $knowledgeBase = self::knowledgeBase();
            self::$agent = AiAgent::create(self::generateExampleAiAgent([$knowledgeBase->id]));
        }
        return self::$agent;
    }

    public static function chat()
    {
        if (is_null(self::$chat)) {
            $agent = self::agent();
            self::$chat = AiChat::create(self::generateExampleAiChat($agent->id));
        }
        return self::$chat;
    }

    public static function posted()
    {
        if (is_null(self::$posted)) {
            $chat = self::chat();
            self::$posted = AiMessage::create(new AiMessage(["chatId" => $chat->id, "text" => "Say hello and mention order 123."]), ["chatName"]);
        }
        return self::$posted;
    }

    public static function voice()
    {
        $audio = self::audio();
        if (is_null($audio)) {
            return null;
        }
        if (is_null(self::$voice)) {
            self::register();
            self::$voice = AiVoice::create(new AiVoice(["audio" => $audio, "name" => self::name("sdk-php-voice")]));
        }
        return self::$voice;
    }

    public static function audio()
    {
        foreach (AiSpeech::query() as $speech) {
            if ($speech->status != "success") {
                continue;
            }
            return AiSpeech::get($speech->id)->audio;
        }
        return null;
    }

    public static function speakingVoice()
    {
        foreach (AiVoice::query() as $voice) {
            if ($voice->status == "success") {
                return $voice;
            }
        }
        return null;
    }

    public static function generateExampleAiAgent($knowledgeBaseIds = null)
    {
        $params = [
            "name" => self::name("sdk-php-agent"),
            "model" => "bender-1.0",
            "systemPrompt" => "Answer in one short sentence.",
            "metadataSchema" => ["order_id" => ["type" => "string", "description" => "Order the customer mentions"]]
        ];
        if (!is_null($knowledgeBaseIds)) {
            $params["knowledgeBaseIds"] = $knowledgeBaseIds;
        }
        return new AiAgent($params);
    }

    public static function generateExampleAiChat($agentId)
    {
        return new AiChat([
            "agentId" => $agentId,
            "title" => self::name("sdk-php-chat"),
            "tags" => ["sdk-php", "test"],
            "context" => ["orderId" => "123", "isUrgent" => true]
        ]);
    }

    public static function run($title, $test)
    {
        echo "\n\t- " . $title;
        if ($test() === false) {
            echo " - SKIPPED";
            return;
        }
        echo " - OK";
    }

    public static function assertRaises($class, $call)
    {
        try {
            $call();
        } catch (\Exception $e) {
            if ($e instanceof $class) {
                return;
            }
            throw $e;
        }
        throw new Exception("failed: nothing was raised");
    }

    public static function assertPageLimitRaises($class, $limit)
    {
        self::assertRaises("StarkCore\Error\InputErrors", function () use ($class, $limit) {
            $class::page(["limit" => $limit]);
        });
    }

    public static function pagesContain($class, $id)
    {
        $options = ["limit" => 100];
        do {
            list($entities, $cursor) = $class::page($options);
            foreach ($entities as $entity) {
                if ($entity->id == $id) {
                    return true;
                }
            }
            $options["cursor"] = $cursor;
        } while (!is_null($cursor));
        return false;
    }

    public static function teardown()
    {
        $created = [
            ["StarkInfra\AiChat", self::$chat],
            ["StarkInfra\AiAgent", self::$agent],
            ["StarkInfra\AiKnowledgeBase", self::$knowledgeBase],
            ["StarkInfra\AiVoice", self::$voice]
        ];
        foreach ($created as list($class, $entity)) {
            if (is_null($entity)) {
                continue;
            }
            $class::delete([$entity->id]);
        }
        self::$chat = self::$agent = self::$knowledgeBase = self::$voice = null;
    }

    private static function register()
    {
        if (self::$registered) {
            return;
        }
        self::$registered = true;
        register_shutdown_function([self::class, "teardown"]);
    }

    private static function name($prefix)
    {
        return $prefix . "-" . bin2hex(random_bytes(6));
    }
}
