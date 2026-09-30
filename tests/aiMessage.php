<?php

namespace Test\AiMessage;
use \Exception;
use StarkInfra\AiAgent;
use StarkInfra\AiChat;
use StarkInfra\AiMessage;
use Test\Utils\AgentExample;
use StarkCore\Error\InputErrors;

include_once("tests/utils/aiAgent.php");


class TestAiMessage
{
    public $agent;
    public $chat;
    public $posted;

    public function setUp()
    {
        $this->agent = AiAgent::create(AgentExample::generateExampleAiAgent());
        $this->chat = AiChat::create(new AiChat(["agentId" => $this->agent->id, "title" => "sdk-php-message-" . bin2hex(random_bytes(6))]));
    }

    public function tearDown()
    {
        AiChat::delete([$this->chat->id]);
        AiAgent::delete([$this->agent->id]);
    }

    public function create()
    {
        $this->posted = AiMessage::create(new AiMessage(["chatId" => $this->chat->id, "text" => "Say hello and mention order 123."]), ["chatName"]);

        if (count($this->posted) != 2 || $this->posted[0]->sender != "user" || $this->posted[1]->sender != "system") {
            throw new Exception("failed");
        }
        foreach ($this->posted as $message) {
            if ($message->chatId != $this->chat->id || !($message->created instanceof \DateTime)) {
                throw new Exception("failed");
            }
            if (!is_string($message->chatName) || $message->chatName == "") {
                throw new Exception("failed");
            }
        }
        if (!is_array($this->posted[1]->metadata)) {
            throw new Exception("failed");
        }
    }

    public function queryReturnsTheWholeHistoryOfTheChat()
    {
        $found = iterator_to_array(AiMessage::query(["chatId" => $this->chat->id]), false);

        if ($this->ids($found) != $this->ids($this->posted)) {
            throw new Exception("failed");
        }
    }

    public function queryWithLimitStopsAtTheLimit()
    {
        $found = iterator_to_array(AiMessage::query(["chatId" => $this->chat->id, "limit" => 1]), false);

        if (count($found) != 1) {
            throw new Exception("failed");
        }
    }

    public function queryWithoutChatIdReadsTheWorkspaceHistory()
    {
        $found = iterator_to_array(AiMessage::query(["limit" => 1]), false);

        if (count($found) != 1 || !($found[0] instanceof AiMessage)) {
            throw new Exception("failed");
        }
    }

    public function pageLeadsToTheNextPage()
    {
        list($first, $cursor) = AiMessage::page(["chatId" => $this->chat->id, "limit" => 1]);
        if (count($first) != 1 || is_null($cursor)) {
            throw new Exception("failed");
        }

        list($second) = AiMessage::page(["chatId" => $this->chat->id, "cursor" => $cursor, "limit" => 1]);

        if (count($second) != 1 || $second[0]->id == $first[0]->id) {
            throw new Exception("failed");
        }
    }

    public function pageOfTheWholeHistoryEndsWithANullCursor()
    {
        list($messages, $cursor) = AiMessage::page(["chatId" => $this->chat->id, "limit" => 100]);

        if ($this->ids($messages) != $this->ids($this->posted) || !is_null($cursor)) {
            throw new Exception("failed");
        }
    }

    public function createRoundTripsAccentedAndEmojiText()
    {
        $text = "Ol\u{e1} \u{2014} \u{201c}x\u{201d} \u{20ac}5 \u{1f600}. Repeat this sentence.";

        $messages = AiMessage::create(new AiMessage(["chatId" => $this->chat->id, "text" => $text]));

        if ($messages[0]->text !== $text) {
            throw new Exception("failed");
        }
    }

    public function queryWithInvalidLimit()
    {
        $error = false;
        try {
            iterator_to_array(AiMessage::query(["limit" => -1]));
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
            AiMessage::page(["limit" => 101]);
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function createInAnUnknownChat()
    {
        $error = false;
        try {
            AiMessage::create(new AiMessage(["chatId" => "0000000000000000", "text" => "hi"]));
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }

    private function ids($messages)
    {
        $ids = [];
        foreach ($messages as $message) {
            $ids[] = $message->id;
        }
        sort($ids);
        return $ids;
    }
}

echo "\n\nAiMessage:";

$test = new TestAiMessage();
$test->setUp();

echo "\n\t- create returns the user message and the answer";
$test->create();
echo " - OK";

echo "\n\t- query returns the whole history of the chat";
$test->queryReturnsTheWholeHistoryOfTheChat();
echo " - OK";

echo "\n\t- query with limit stops at the limit";
$test->queryWithLimitStopsAtTheLimit();
echo " - OK";

echo "\n\t- query without a chat id reads the workspace history";
$test->queryWithoutChatIdReadsTheWorkspaceHistory();
echo " - OK";

echo "\n\t- page leads to the next page";
$test->pageLeadsToTheNextPage();
echo " - OK";

echo "\n\t- page of the whole history ends with a null cursor";
$test->pageOfTheWholeHistoryEndsWithANullCursor();
echo " - OK";

echo "\n\t- create round-trips accented and emoji text";
$test->createRoundTripsAccentedAndEmojiText();
echo " - OK";

echo "\n\t- query with invalid limit";
$test->queryWithInvalidLimit();
echo " - OK";

echo "\n\t- page with invalid limit";
$test->pageWithInvalidLimit();
echo " - OK";

echo "\n\t- create in an unknown chat";
$test->createInAnUnknownChat();
echo " - OK";

$test->tearDown();
