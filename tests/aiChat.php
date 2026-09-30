<?php

namespace Test\AiChat;
use \Exception;
use StarkInfra\AiAgent;
use StarkInfra\AiChat;
use Test\Utils\AgentExample;
use StarkCore\Error\InputErrors;

include_once("tests/utils/aiAgent.php");


class TestAiChat
{
    public $agent;
    public $chat;

    public function setUp()
    {
        $this->agent = AiAgent::create(AgentExample::generateExampleAiAgent());
        $this->chat = AiChat::create(TestAiChat::exampleChat($this->agent->id));
    }

    public function tearDown()
    {
        AiChat::delete([$this->chat->id]);
        AiAgent::delete([$this->agent->id]);
    }

    public function create()
    {
        if (!is_string($this->chat->id) || $this->chat->agentId != $this->agent->id || !($this->chat->updated instanceof \DateTime)) {
            throw new Exception("failed");
        }
        if ($this->chat->tags != ["sdk-php", "test"] || $this->chat->context != ["orderId" => "123", "isUrgent" => true]) {
            throw new Exception("failed");
        }
    }

    public function createWithoutOptionalsReturnsEmptyTagsAndContext()
    {
        $chat = AiChat::create(new AiChat(["agentId" => $this->agent->id]));
        AiChat::delete([$chat->id]);

        if ($chat->tags != [] || $chat->context != []) {
            throw new Exception("failed");
        }
    }

    public function getWithExpand()
    {
        $fetched = AiChat::get($this->chat->id, ["expand" => ["agentName"]]);

        if ($fetched->id != $this->chat->id || $fetched->agentName != $this->agent->name) {
            throw new Exception("failed");
        }
        if (!is_null(AiChat::get($this->chat->id)->agentName)) {
            throw new Exception("failed");
        }
    }

    public function queryFiltersByTags()
    {
        foreach (AiChat::query(["tags" => ["sdk-php", "test"], "expand" => ["agentName"]]) as $chat) {
            if ($chat->id == $this->chat->id && $chat->agentName == $this->agent->name) {
                return;
            }
        }
        throw new Exception("failed");
    }

    public function queryWithUnknownTagIsEmpty()
    {
        $found = iterator_to_array(AiChat::query(["tags" => ["no-chat-has-this-tag"]]), false);

        if (count($found) != 0) {
            throw new Exception("failed");
        }
    }

    public function pageLeadsToTheCreatedChat()
    {
        $options = ["limit" => 100];
        do {
            list($found, $cursor) = AiChat::page($options);
            foreach ($found as $chat) {
                if ($chat->id == $this->chat->id) {
                    return;
                }
            }
            $options["cursor"] = $cursor;
        } while (!is_null($cursor));
        throw new Exception("failed");
    }

    public function updateRenamesTheChatAndKeepsTheRest()
    {
        $renamed = AiChat::update($this->chat->id, ["title" => "renamed-by-sdk"]);

        if ($renamed->title != "renamed-by-sdk" || $renamed->agentId != $this->chat->agentId) {
            throw new Exception("failed");
        }
        if ($renamed->tags != $this->chat->tags || $renamed->context != $this->chat->context) {
            throw new Exception("failed");
        }
    }

    public function updateReplacesTagsAndContext()
    {
        $changed = AiChat::update($this->chat->id, ["tags" => ["changed"], "context" => ["orderId" => "456"]]);

        if ($changed->tags != ["changed"] || $changed->context != ["orderId" => "456"]) {
            throw new Exception("failed");
        }
    }

    public function updateWithEmptyValuesClearsTheFields()
    {
        $cleared = AiChat::update($this->chat->id, ["tags" => [], "context" => new \stdClass()]);

        if ($cleared->tags !== [] || count($cleared->context) != 0) {
            throw new Exception("failed");
        }
    }

    public function delete()
    {
        $chat = AiChat::create(new AiChat(["agentId" => $this->agent->id, "title" => "sdk-php-delete"]));

        $deleted = AiChat::delete([$chat->id]);

        if (count($deleted) != 1 || $deleted[0]->id != $chat->id) {
            throw new Exception("failed");
        }
    }

    public function queryWithInvalidLimit()
    {
        $error = false;
        try {
            iterator_to_array(AiChat::query(["limit" => -1]));
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function pageWithInvalidLimit()
    {
        $error = false;
        try {
            AiChat::page(["limit" => 101]);
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function getUnknownId()
    {
        $error = false;
        try {
            AiChat::get("0000000000000000");
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public static function exampleChat($agentId)
    {
        return new AiChat([
            "agentId" => $agentId,
            "title" => "sdk-php-chat-" . bin2hex(random_bytes(6)),
            "tags" => ["sdk-php", "test"],
            "context" => ["orderId" => "123", "isUrgent" => true]
        ]);
    }
}

echo "\n\nAiChat:";

$test = new TestAiChat();
$test->setUp();

echo "\n\t- create";
$test->create();
echo " - OK";

echo "\n\t- create without optionals returns empty tags and context";
$test->createWithoutOptionalsReturnsEmptyTagsAndContext();
echo " - OK";

echo "\n\t- get with expand";
$test->getWithExpand();
echo " - OK";

echo "\n\t- query filters by tags";
$test->queryFiltersByTags();
echo " - OK";

echo "\n\t- query with an unknown tag is empty";
$test->queryWithUnknownTagIsEmpty();
echo " - OK";

echo "\n\t- page leads to the created chat";
$test->pageLeadsToTheCreatedChat();
echo " - OK";

echo "\n\t- update renames the chat and keeps the rest";
$test->updateRenamesTheChatAndKeepsTheRest();
echo " - OK";

echo "\n\t- update replaces tags and context";
$test->updateReplacesTagsAndContext();
echo " - OK";

echo "\n\t- update with empty values clears the fields";
$test->updateWithEmptyValuesClearsTheFields();
echo " - OK";

echo "\n\t- delete";
$test->delete();
echo " - OK";

echo "\n\t- query with invalid limit";
$test->queryWithInvalidLimit();
echo " - OK";

echo "\n\t- page with invalid limit";
$test->pageWithInvalidLimit();
echo " - OK";

echo "\n\t- get unknown id";
$test->getUnknownId();
echo " - OK";

$test->tearDown();
