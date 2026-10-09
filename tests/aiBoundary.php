<?php

namespace StarkCore\Utils;
use Exception;

require_once(__DIR__ . "/../vendor/autoload.php");


class Response
{
    public $status;
    public $content;

    function __construct($status, $content)
    {
        $this->status = $status;
        $this->content = $content;
    }

    function json()
    {
        return json_decode($this->content, true);
    }
}


class Request
{
    public static $sent = [];
    public static $answers = [];

    public static function fetch($host, $sdkVersion, $user, $method, $path, $payload = null, $query = null, $apiVersion = "v2", $language = "en-US", $timeout = 15, $prefix = null, $throwError = true)
    {
        self::$sent[] = [
            "method" => $method,
            "url" => $path . (is_null($query) ? "" : URL::encode($query)),
            "payload" => is_null($payload) ? null : json_decode(json_encode($payload), true),
            "payloadJson" => is_null($payload) ? null : json_encode($payload)
        ];
        return new Response(200, json_encode(array_shift(self::$answers)));
    }
}


namespace Test\AiBoundary;
use Exception;
use StarkCore\Utils\Request;
use StarkInfra\AiAgent;
use StarkInfra\AiChat;
use StarkInfra\AiKnowledgeBase;
use StarkInfra\AiMessage;
use StarkInfra\AiSpeech;
use StarkInfra\AiTranscript;
use StarkInfra\AiVoice;
use StarkInfra\Key;
use StarkInfra\Project;
use StarkInfra\Settings;


class TestAiAtTheHttpBoundary
{
    private $agentJson = [
        "id" => "5740688905863168",
        "name" => "Support assistant",
        "model" => "bender-1.0",
        "systemPrompt" => "Answer in one short sentence.",
        "voiceId" => "",
        "knowledgeBaseIds" => ["5083538508480512"],
        "metadataSchema" => ["order_id" => ["type" => "string"]],
        "created" => "2026-09-30T15:42:56.879325+00:00",
        "updated" => "2026-09-30T15:42:56.879334+00:00"
    ];

    private $chatJson = [
        "id" => "5632499082330112",
        "agentId" => "5740688905863168",
        "title" => "Order 123",
        "tags" => ["vip", "order"],
        "context" => ["orderId" => "123"],
        "agentName" => "Support assistant",
        "updated" => "2026-10-01T14:28:02+00:00"
    ];

    private $knowledgeBaseJson = [
        "id" => "6767676767676767",
        "name" => "Public Documentation",
        "rootUrl" => "https://docs.starkinfra.com",
        "isRecursive" => false,
        "status" => "success",
        "tags" => [],
        "created" => "2022-01-01T00:00:00.000000+00:00",
        "updated" => "2022-01-02T00:00:00.000000+00:00"
    ];

    private $messagesJson = [
        ["id" => "5642368648740864", "chatId" => "5632499082330112", "sender" => "user", "text" => "Say hello.", "speech" => "Say hello.", "metadata" => [], "model" => "bender-1.0", "created" => "2026-10-01T14:28:02.652375+00:00"],
        ["id" => "5079418695319552", "chatId" => "5632499082330112", "sender" => "system", "text" => "Hello!", "speech" => "Hello!", "metadata" => ["order_id" => "123"], "model" => "bender-1.0", "created" => "2026-10-01T14:28:02.653375+00:00"]
    ];

    public function voiceCreateSendsOnlyTheCreatableFields()
    {
        $json = [
            "id" => "5631671361601536",
            "name" => "Helena",
            "description" => "Calm voice",
            "language" => "portuguese",
            "gender" => "female",
            "status" => "processing",
            "errors" => [],
            "created" => "2026-10-01T14:28:24.566332+00:00",
            "updated" => "2026-10-01T14:28:24.566342+00:00"
        ];
        Request::$answers = [["voice" => $json]];

        $voice = AiVoice::create(new AiVoice(["audio" => "SUQzBAAAAAAA", "name" => "Helena", "description" => "Calm voice", "language" => "portuguese", "gender" => "female"]));

        $this->assertSent("POST", "ai-voice", [
            "audio" => "SUQzBAAAAAAA",
            "name" => "Helena",
            "description" => "Calm voice",
            "language" => "portuguese",
            "gender" => "female"
        ]);
        $this->assertTrue($voice->id == "5631671361601536" && $voice->status == "processing" && $voice->errors === []);
        $this->assertTrue($voice->created instanceof \DateTime);
    }

    public function voiceCreateDropsAbsentOptionalFields()
    {
        Request::$answers = [["voice" => ["id" => "1", "status" => "processing"]]];

        AiVoice::create(new AiVoice(["audio" => "SUQzBAAAAAAA"]));

        $this->assertSentJson('{"audio":"SUQzBAAAAAAA","name":null,"description":null,"language":null,"gender":null}');
    }

    public function voiceQuerySendsTheLimit()
    {
        Request::$answers = [["voices" => [["id" => "1", "name" => "Helena"], ["id" => "2", "name" => "Rui"]]]];

        $voices = iterator_to_array(AiVoice::query(["limit" => 2]), false);

        $this->assertSent("GET", "ai-voice?limit=2", null);
        $this->assertTrue(count($voices) == 2 && $voices[1]->name == "Rui");
    }

    public function voicePageReturnsTheVoicesAndTheCursor()
    {
        Request::$answers = [["cursor" => "next-page", "voices" => [["id" => "1", "name" => "Helena"]]]];

        list($voices, $cursor) = AiVoice::page(["cursor" => "previous-page", "limit" => 1]);

        $this->assertSent("GET", "ai-voice?cursor=previous-page&limit=1", null);
        $this->assertTrue(count($voices) == 1 && $voices[0] instanceof AiVoice && $cursor == "next-page");
    }

    public function voicePageOfTheLastPageHasANullCursor()
    {
        Request::$answers = [["cursor" => null, "voices" => [["id" => "1"]]]];

        list($voices, $cursor) = AiVoice::page();

        $this->assertTrue(count($voices) == 1 && is_null($cursor));
    }

    public function voiceDeleteSendsIdsInTheQueryString()
    {
        Request::$answers = [["voices" => [["id" => "5631671361601536", "name" => "Helena"]]]];

        $deleted = AiVoice::delete(["5631671361601536", "5631671361601537"]);

        $this->assertSent("DELETE", "ai-voice?ids=5631671361601536%2C5631671361601537", null);
        $this->assertTrue(count($deleted) == 1 && $deleted[0] instanceof AiVoice && $deleted[0]->id == "5631671361601536");
    }

    public function speechCreateSendsOnlyVoiceIdAndText()
    {
        $json = [
            "id" => "5646488461901824",
            "voiceId" => "5632499082330112",
            "text" => "Short test.",
            "status" => "success",
            "audio" => "SUQzBAAAAAAA",
            "errors" => [],
            "created" => "2026-10-01T14:28:06.942491+00:00",
            "updated" => "2026-10-01T14:28:07.605185+00:00"
        ];
        Request::$answers = [["speech" => $json]];

        $speech = AiSpeech::create(new AiSpeech(["voiceId" => "5632499082330112", "text" => "Short test."]));

        $this->assertSent("POST", "ai-speech", ["voiceId" => "5632499082330112", "text" => "Short test."]);
        $this->assertTrue($speech->status == "success" && $speech->audio == "SUQzBAAAAAAA");
    }

    public function speechGetSendsExpandInTheQueryString()
    {
        Request::$answers = [["speech" => ["id" => "5646488461901824", "voiceName" => "Helena"]]];

        $speech = AiSpeech::get("5646488461901824", ["expand" => ["voiceName"]]);

        $this->assertSent("GET", "ai-speech/5646488461901824?expand=voiceName", null);
        $this->assertTrue($speech->id == "5646488461901824" && $speech->voiceName == "Helena");
    }

    public function speechQueryReadsSpeechesAndSendsExpand()
    {
        Request::$answers = [["speeches" => [["id" => "5646488461901824", "voiceId" => "5632499082330112", "voiceName" => "Helena"]]]];

        $speeches = iterator_to_array(AiSpeech::query(["expand" => ["voiceName"]]), false);

        $this->assertSent("GET", "ai-speech?expand=voiceName", null);
        $this->assertTrue(count($speeches) == 1 && $speeches[0]->voiceName == "Helena");
    }

    public function speechQueryFollowsTheCursorThroughEmptyPages()
    {
        Request::$answers = [
            ["cursor" => "first", "speeches" => []],
            ["cursor" => "second", "speeches" => []],
            ["cursor" => null, "speeches" => [["id" => "1"]]]
        ];

        $speeches = iterator_to_array(AiSpeech::query(), false);

        $this->assertSentAt(0, "GET", "ai-speech", null);
        $this->assertSentAt(1, "GET", "ai-speech?cursor=first", null);
        $this->assertSentAt(2, "GET", "ai-speech?cursor=second", null);
        $this->assertTrue(count($speeches) == 1 && $speeches[0]->id == "1");
    }

    public function speechQueryHonoursTheLimitAcrossPages()
    {
        Request::$answers = [
            ["cursor" => "next-page", "speeches" => $this->entities(100)],
            ["cursor" => "more", "speeches" => $this->entities(50)]
        ];

        $speeches = iterator_to_array(AiSpeech::query(["limit" => 150]), false);

        $this->assertSentAt(0, "GET", "ai-speech?limit=100", null);
        $this->assertSentAt(1, "GET", "ai-speech?limit=50&cursor=next-page", null);
        $this->assertTrue(count($speeches) == 150 && count(Request::$sent) == 2);
    }

    public function speechPageReturnsTheSpeechesAndTheCursor()
    {
        Request::$answers = [["cursor" => "next-page", "speeches" => [["id" => "1"]]]];

        list($speeches, $cursor) = AiSpeech::page(["cursor" => "previous-page", "limit" => 1, "expand" => ["voiceName"]]);

        $this->assertSent("GET", "ai-speech?cursor=previous-page&limit=1&expand=voiceName", null);
        $this->assertTrue(count($speeches) == 1 && $speeches[0] instanceof AiSpeech && $cursor == "next-page");
    }

    public function speechPageOfTheLastPageHasANullCursor()
    {
        Request::$answers = [["cursor" => null, "speeches" => [["id" => "1"]]]];

        list($speeches, $cursor) = AiSpeech::page();

        $this->assertTrue(count($speeches) == 1 && is_null($cursor));
    }

    public function transcriptCreateSendsOnlyTheAudio()
    {
        $json = [
            "id" => "5147403464212480",
            "text" => "This is a short recording used to test the transcription service.",
            "status" => "success",
            "errors" => [],
            "created" => "2026-10-01T14:28:04.482326+00:00",
            "updated" => "2026-10-01T14:28:05.752389+00:00"
        ];
        Request::$answers = [["transcript" => $json]];

        $transcript = AiTranscript::create(new AiTranscript(["audio" => "SUQzBAAAAAAA"]));

        $this->assertSent("POST", "ai-transcript", ["audio" => "SUQzBAAAAAAA"]);
        $this->assertTrue($transcript->status == "success" && $transcript->text != "");
    }

    public function transcriptQuerySendsTheLimit()
    {
        Request::$answers = [["transcripts" => [["id" => "5147403464212480", "text" => "hi"]]]];

        $transcripts = iterator_to_array(AiTranscript::query(["limit" => 1]), false);

        $this->assertSent("GET", "ai-transcript?limit=1", null);
        $this->assertTrue(count($transcripts) == 1 && $transcripts[0]->text == "hi");
    }

    public function transcriptPageReturnsTheTranscriptsAndTheCursor()
    {
        Request::$answers = [["cursor" => "next-page", "transcripts" => [["id" => "1"]]]];

        list($transcripts, $cursor) = AiTranscript::page(["limit" => 1]);

        $this->assertSent("GET", "ai-transcript?limit=1", null);
        $this->assertTrue(count($transcripts) == 1 && $transcripts[0] instanceof AiTranscript && $cursor == "next-page");
    }

    public function agentCreateSendsTheFieldsAndLeavesSchemaKeysAlone()
    {
        Request::$answers = [["agent" => $this->agentJson]];

        AiAgent::create(new AiAgent([
            "name" => "Support assistant",
            "model" => "bender-1.0",
            "systemPrompt" => "Be brief.",
            "voiceId" => "5632499082330112",
            "knowledgeBaseIds" => [],
            "metadataSchema" => ["order_id" => ["type" => "string"], "isUrgent" => ["type" => "boolean"]]
        ]));

        $this->assertSent("POST", "ai-agent", [
            "name" => "Support assistant",
            "model" => "bender-1.0",
            "systemPrompt" => "Be brief.",
            "voiceId" => "5632499082330112",
            "knowledgeBaseIds" => [],
            "metadataSchema" => ["order_id" => ["type" => "string"], "isUrgent" => ["type" => "boolean"]]
        ]);
    }

    public function agentGetExpandParsesKnowledgeBases()
    {
        $knowledgeBase = ["id" => "5083538508480512", "name" => "Docs", "rootUrl" => "https://docs.starkinfra.com", "status" => "success"];
        Request::$answers = [["agent" => $this->agentJson + ["knowledgeBases" => [$knowledgeBase]]]];

        $agent = AiAgent::get("5740688905863168", ["expand" => ["knowledgeBases"]]);

        $this->assertSent("GET", "ai-agent/5740688905863168?expand=knowledgeBases", null);
        $this->assertTrue(count($agent->knowledgeBases) == 1 && $agent->knowledgeBases[0] instanceof AiKnowledgeBase);
        $this->assertTrue($agent->metadataSchema == ["order_id" => ["type" => "string"]]);
    }

    public function agentQuerySendsExpandAndLimit()
    {
        Request::$answers = [["agents" => [$this->agentJson]]];

        $agents = iterator_to_array(AiAgent::query(["limit" => 5, "expand" => ["knowledgeBases"]]), false);

        $this->assertSent("GET", "ai-agent?expand=knowledgeBases&limit=5", null);
        $this->assertTrue(count($agents) == 1 && $agents[0]->name == "Support assistant");
    }

    public function agentPageReturnsTheAgentsAndTheCursor()
    {
        Request::$answers = [["cursor" => "next-page", "agents" => [$this->agentJson]]];

        list($agents, $cursor) = AiAgent::page(["cursor" => "previous-page", "limit" => 1, "expand" => ["knowledgeBases"]]);

        $this->assertSent("GET", "ai-agent?cursor=previous-page&limit=1&expand=knowledgeBases", null);
        $this->assertTrue(count($agents) == 1 && $agents[0] instanceof AiAgent && $cursor == "next-page");
    }

    public function agentPageOfTheLastPageHasANullCursor()
    {
        Request::$answers = [["cursor" => null, "agents" => [$this->agentJson]]];

        list($agents, $cursor) = AiAgent::page();

        $this->assertTrue(count($agents) == 1 && is_null($cursor));
    }

    public function agentUpdateNamesAllSixKeysAndSendsNullForTheAbsentOnesWithoutReadingTheAgent()
    {
        Request::$answers = [["agent" => $this->agentJson]];

        AiAgent::update("5740688905863168", ["name" => "Renamed"]);

        $this->assertTrue(count(Request::$sent) == 1);
        $this->assertSent("PATCH", "ai-agent/5740688905863168", [
            "name" => "Renamed",
            "model" => null,
            "systemPrompt" => null,
            "voiceId" => null,
            "knowledgeBaseIds" => null,
            "metadataSchema" => null
        ]);
    }

    public function agentCreateSendsEmptyMapsAsObjectsAndKeepsNestedNulls()
    {
        Request::$answers = [["agent" => $this->agentJson], ["agent" => $this->agentJson]];

        AiAgent::create(new AiAgent(["name" => "a", "model" => "bender-1.0", "metadataSchema" => []]));
        $this->assertSentJson('{"name":"a","model":"bender-1.0","systemPrompt":null,"voiceId":null,"knowledgeBaseIds":null,"metadataSchema":{}}');

        AiAgent::create(new AiAgent(["name" => "a", "model" => "bender-1.0", "metadataSchema" => ["isUrgent" => ["type" => "boolean", "description" => null]]]));
        $this->assertSentJson('{"name":"a","model":"bender-1.0","systemPrompt":null,"voiceId":null,"knowledgeBaseIds":null,"metadataSchema":{"isUrgent":{"type":"boolean","description":null}}}');
    }

    public function agentCreateKeepsNonAsciiTextAsValidUtf8Json()
    {
        Request::$answers = [["agent" => $this->agentJson]];
        $text = "Ol\u{e1} \u{2014} \u{201c}x\u{201d} \u{20ac}5 \u{1f600}";

        AiAgent::create(new AiAgent(["name" => $text, "model" => "bender-1.0", "systemPrompt" => $text]));

        $sent = Request::$sent[0]["payloadJson"];
        $decoded = json_decode($sent, true);
        $this->assertTrue(json_last_error() === JSON_ERROR_NONE && $decoded["name"] === $text && $decoded["systemPrompt"] === $text);
    }

    public function chatCreateSendsEmptyContextAsAnObjectAndKeepsNestedNullsAndNonAscii()
    {
        Request::$answers = [["chat" => $this->chatJson], ["chat" => $this->chatJson]];

        AiChat::create(new AiChat(["agentId" => "1", "tags" => [], "context" => []]));
        $this->assertSentJson('{"agentId":"1","title":null,"tags":[],"context":{}}');

        $text = "Ol\u{e1} \u{1f600}";
        AiChat::create(new AiChat(["agentId" => "1", "title" => $text, "context" => ["isUrgent" => null, "note" => $text]]));
        $decoded = json_decode(Request::$sent[1]["payloadJson"], true);
        $this->assertTrue($decoded["title"] === $text && $decoded["context"] === ["isUrgent" => null, "note" => $text]);
    }

    public function messageCreateKeepsNonAsciiText()
    {
        Request::$answers = [["messages" => $this->messagesJson]];
        $text = "Ol\u{e1} \u{2014} \u{201c}x\u{201d} \u{20ac}5 \u{1f600}";

        AiMessage::create(new AiMessage(["chatId" => "1", "text" => $text]));

        $this->assertTrue(json_decode(Request::$sent[0]["payloadJson"], true)["text"] === $text);
    }

    public function speechAndVoiceCreateKeepNonAsciiText()
    {
        Request::$answers = [["speech" => ["id" => "1"]], ["voice" => ["id" => "2"]]];
        $text = "Ol\u{e1} \u{1f600}";

        AiSpeech::create(new AiSpeech(["voiceId" => "1", "text" => $text]));
        AiVoice::create(new AiVoice(["audio" => "SUQz", "name" => $text, "description" => $text]));

        $this->assertTrue(json_decode(Request::$sent[0]["payloadJson"], true)["text"] === $text);
        $this->assertTrue(json_decode(Request::$sent[1]["payloadJson"], true)["name"] === $text);
    }

    public function knowledgeBaseCreateAndUpdateKeepNonAsciiNames()
    {
        Request::$answers = [["knowledgeBase" => $this->knowledgeBaseJson], ["knowledgeBase" => $this->knowledgeBaseJson]];
        $text = "Documenta\u{e7}\u{e3}o \u{1f600}";

        AiKnowledgeBase::create(new AiKnowledgeBase(["name" => $text, "rootUrl" => "https://docs.starkinfra.com"]));
        AiKnowledgeBase::update("1", ["name" => $text, "tags" => []]);

        $this->assertTrue(json_decode(Request::$sent[0]["payloadJson"], true)["name"] === $text);
        $this->assertSentJson(json_encode(["name" => $text, "tags" => []]));
    }

    public function agentUpdateSendsEmptyValuesToClearTheFields()
    {
        Request::$answers = [["agent" => $this->agentJson]];

        AiAgent::update("5740688905863168", [
            "systemPrompt" => "",
            "voiceId" => "",
            "knowledgeBaseIds" => [],
            "metadataSchema" => []
        ]);

        $this->assertSentJson('{"name":null,"model":null,"systemPrompt":"","voiceId":"","knowledgeBaseIds":[],"metadataSchema":{}}');
    }

    public function agentUpdateSendsTheSchemaKeysAsWritten()
    {
        Request::$answers = [["agent" => $this->agentJson]];

        AiAgent::update("5740688905863168", ["metadataSchema" => ["isUrgent" => ["type" => "boolean", "description" => null]]]);

        $this->assertSentJson('{"name":null,"model":null,"systemPrompt":null,"voiceId":null,"knowledgeBaseIds":null,"metadataSchema":{"isUrgent":{"type":"boolean","description":null}}}');
    }

    public function agentDeleteSendsIdsInTheQueryString()
    {
        Request::$answers = [["agents" => [$this->agentJson]]];

        $deleted = AiAgent::delete(["5740688905863168", "5740688905863169"]);

        $this->assertSent("DELETE", "ai-agent?ids=5740688905863168%2C5740688905863169", null);
        $this->assertTrue(count($deleted) == 1 && $deleted[0] instanceof AiAgent && $deleted[0]->id == "5740688905863168");
    }

    public function chatCreateSendsTagsAndContext()
    {
        Request::$answers = [["chat" => $this->chatJson]];

        $chat = AiChat::create(new AiChat([
            "agentId" => "5740688905863168",
            "title" => "Order 123",
            "tags" => ["vip", "order"],
            "context" => ["orderId" => "123"]
        ]));

        $this->assertSent("POST", "ai-chat", [
            "agentId" => "5740688905863168",
            "title" => "Order 123",
            "tags" => ["vip", "order"],
            "context" => ["orderId" => "123"]
        ]);
        $this->assertTrue($chat->tags == ["vip", "order"] && $chat->context == ["orderId" => "123"] && $chat->agentName == "Support assistant");
    }

    public function chatCreateWithoutOptionalsSendsOnlyTheAgentId()
    {
        Request::$answers = [["chat" => $this->chatJson]];

        AiChat::create(new AiChat(["agentId" => "5740688905863168"]));

        $this->assertSentJson('{"agentId":"5740688905863168","title":null,"tags":null,"context":null}');
    }

    public function chatUpdateSendsTagsAndContextAndNullForTheAbsentKeys()
    {
        Request::$answers = [["chat" => $this->chatJson]];

        AiChat::update("5632499082330112", ["tags" => ["vip"], "context" => ["orderId" => "456"]]);

        $this->assertSent("PATCH", "ai-chat/5632499082330112", [
            "title" => null,
            "agentId" => null,
            "tags" => ["vip"],
            "context" => ["orderId" => "456"]
        ]);
    }

    public function chatUpdateWithoutFieldsSendsEveryKeyAsNull()
    {
        Request::$answers = [["chat" => $this->chatJson]];

        AiChat::update("5632499082330112");

        $this->assertSentJson('{"title":null,"agentId":null,"tags":null,"context":null}');
    }

    public function chatUpdateSendsEmptyValuesToClearTheFields()
    {
        Request::$answers = [["chat" => $this->chatJson]];

        AiChat::update("5632499082330112", ["title" => "", "tags" => [], "context" => []]);

        $this->assertSentJson('{"title":"","agentId":null,"tags":[],"context":{}}');
    }

    public function chatGetSendsExpandInTheQueryString()
    {
        Request::$answers = [["chat" => $this->chatJson]];

        AiChat::get("5632499082330112", ["expand" => ["agentName"]]);

        $this->assertSent("GET", "ai-chat/5632499082330112?expand=agentName", null);
    }

    public function chatQuerySendsTagsCommaSeparated()
    {
        Request::$answers = [["chats" => [$this->chatJson]]];

        $chats = iterator_to_array(AiChat::query(["limit" => 5, "expand" => ["agentName"], "tags" => ["vip", "order"]]), false);

        $this->assertSent("GET", "ai-chat?expand=agentName&tags=vip%2Corder&limit=5", null);
        $this->assertTrue(count($chats) == 1 && $chats[0]->tags == ["vip", "order"]);
    }

    public function chatPageReturnsTheChatsAndTheCursor()
    {
        Request::$answers = [["cursor" => "next-page", "chats" => [$this->chatJson]]];

        list($chats, $cursor) = AiChat::page(["cursor" => "previous-page", "limit" => 1, "tags" => ["vip", "order"]]);

        $this->assertSent("GET", "ai-chat?cursor=previous-page&limit=1&tags=vip%2Corder", null);
        $this->assertTrue(count($chats) == 1 && $chats[0] instanceof AiChat && $cursor == "next-page");
    }

    public function chatPageOfTheLastPageHasANullCursor()
    {
        Request::$answers = [["cursor" => null, "chats" => [$this->chatJson]]];

        list($chats, $cursor) = AiChat::page();

        $this->assertTrue(count($chats) == 1 && is_null($cursor));
    }

    public function chatDeleteSendsIdsInTheQueryString()
    {
        Request::$answers = [["chats" => [["id" => "5632499082330112"]]]];

        $deleted = AiChat::delete(["5632499082330112"]);

        $this->assertSent("DELETE", "ai-chat?ids=5632499082330112", null);
        $this->assertTrue($deleted[0] instanceof AiChat);
    }

    public function messageCreateSendsExpandInTheQueryStringAndNotInTheBody()
    {
        Request::$answers = [["chatName" => "Greeting", "messages" => $this->messagesJson]];

        $messages = AiMessage::create(
            new AiMessage(["chatId" => "5632499082330112", "text" => "Say hello.", "model" => "prime-1.0"]),
            ["chatName"]
        );

        $this->assertSent("POST", "ai-message?expand=chatName", ["chatId" => "5632499082330112", "text" => "Say hello.", "model" => "prime-1.0"]);
        $this->assertTrue($messages[0]->sender == "user" && $messages[1]->sender == "system");
        $this->assertTrue($messages[0]->chatName == "Greeting" && $messages[1]->chatName == "Greeting");
        $this->assertTrue($messages[1]->metadata == ["order_id" => "123"]);
    }

    public function messageCreateWithoutExpandSendsNoQueryAndLeavesChatNameEmpty()
    {
        Request::$answers = [["messages" => $this->messagesJson]];

        $messages = AiMessage::create(new AiMessage(["chatId" => "5632499082330112", "text" => "Say hello."]));

        $this->assertSentJson('{"chatId":"5632499082330112","text":"Say hello.","model":null}');
        $this->assertTrue(is_null($messages[0]->chatName));
    }

    public function messageQueryWithoutChatIdReadsTheWorkspaceHistory()
    {
        Request::$answers = [["messages" => $this->messagesJson]];

        $messages = iterator_to_array(AiMessage::query(["limit" => 2]), false);

        $this->assertSent("GET", "ai-message?limit=2", null);
        $this->assertTrue(count($messages) == 2);
    }

    public function messageQueryFollowsTheCursorAcrossPages()
    {
        Request::$answers = [
            ["cursor" => "next-page", "messages" => [$this->messagesJson[0]]],
            ["cursor" => null, "messages" => [$this->messagesJson[1]]]
        ];

        $messages = iterator_to_array(AiMessage::query(["chatId" => "5632499082330112"]), false);

        $this->assertSentAt(0, "GET", "ai-message?chatId=5632499082330112", null);
        $this->assertSentAt(1, "GET", "ai-message?chatId=5632499082330112&cursor=next-page", null);
        $this->assertTrue(count($messages) == 2 && $messages[0]->id == "5642368648740864" && $messages[1]->id == "5079418695319552");
    }

    public function messagePageReturnsTheMessagesAndTheCursor()
    {
        Request::$answers = [["cursor" => "next-page", "messages" => $this->messagesJson]];

        list($messages, $cursor) = AiMessage::page(["chatId" => "5632499082330112", "cursor" => "previous-page", "limit" => 2]);

        $this->assertSent("GET", "ai-message?chatId=5632499082330112&cursor=previous-page&limit=2", null);
        $this->assertTrue(count($messages) == 2 && $cursor == "next-page");
    }

    public function messagePageWithoutChatIdOmitsItFromTheUrl()
    {
        Request::$answers = [["cursor" => null, "messages" => $this->messagesJson]];

        list($messages, $cursor) = AiMessage::page(["limit" => 2]);

        $this->assertSent("GET", "ai-message?limit=2", null);
        $this->assertTrue(count($messages) == 2 && is_null($cursor));
    }

    public function knowledgeBaseCreateSendsTheFieldsThatWereGiven()
    {
        Request::$answers = [["knowledgeBase" => $this->knowledgeBaseJson]];

        $knowledgeBase = AiKnowledgeBase::create(new AiKnowledgeBase([
            "name" => "Public Documentation",
            "rootUrl" => "https://docs.starkinfra.com",
            "isRecursive" => false,
            "tags" => []
        ]));

        $this->assertSent("POST", "ai-knowledge-base", [
            "name" => "Public Documentation",
            "rootUrl" => "https://docs.starkinfra.com",
            "isRecursive" => false,
            "tags" => []
        ]);
        $this->assertTrue($knowledgeBase->id == "6767676767676767" && $knowledgeBase->status == "success");
    }

    public function knowledgeBaseGetUsesTheIdInThePath()
    {
        Request::$answers = [["knowledgeBase" => $this->knowledgeBaseJson]];

        $knowledgeBase = AiKnowledgeBase::get("6767676767676767");

        $this->assertSent("GET", "ai-knowledge-base/6767676767676767", null);
        $this->assertTrue($knowledgeBase->id == "6767676767676767");
    }

    public function knowledgeBaseQuerySendsIdsCommaSeparatedWithNameAndStatus()
    {
        Request::$answers = [["knowledgeBases" => [$this->knowledgeBaseJson]]];

        $found = iterator_to_array(AiKnowledgeBase::query(["ids" => ["1", "2"], "name" => "docs", "status" => "success"]), false);

        $this->assertSent("GET", "ai-knowledge-base?ids=1%2C2&name=docs&status=success", null);
        $this->assertTrue(count($found) == 1 && $found[0] instanceof AiKnowledgeBase);
    }

    public function knowledgeBaseQueryFollowsTheCursorThroughEmptyPages()
    {
        Request::$answers = [
            ["cursor" => "first", "knowledgeBases" => []],
            ["cursor" => "second", "knowledgeBases" => []],
            ["cursor" => null, "knowledgeBases" => [$this->knowledgeBaseJson]]
        ];

        $found = iterator_to_array(AiKnowledgeBase::query(), false);

        $this->assertSentAt(0, "GET", "ai-knowledge-base", null);
        $this->assertSentAt(1, "GET", "ai-knowledge-base?cursor=first", null);
        $this->assertSentAt(2, "GET", "ai-knowledge-base?cursor=second", null);
        $this->assertTrue(count($found) == 1);
    }

    public function knowledgeBaseQueryHonoursTheLimitAcrossPages()
    {
        Request::$answers = [
            ["cursor" => "next-page", "knowledgeBases" => $this->entities(100)],
            ["cursor" => "more", "knowledgeBases" => $this->entities(50)]
        ];

        $found = iterator_to_array(AiKnowledgeBase::query(["limit" => 150]), false);

        $this->assertSentAt(0, "GET", "ai-knowledge-base?limit=100", null);
        $this->assertSentAt(1, "GET", "ai-knowledge-base?limit=50&cursor=next-page", null);
        $this->assertTrue(count($found) == 150 && count(Request::$sent) == 2);
    }

    public function knowledgeBasePageReturnsTheBasesAndTheCursor()
    {
        Request::$answers = [["cursor" => "next-page", "knowledgeBases" => [$this->knowledgeBaseJson]]];

        list($found, $cursor) = AiKnowledgeBase::page(["cursor" => "previous-page", "limit" => 1, "ids" => ["1", "2"]]);

        $this->assertSent("GET", "ai-knowledge-base?cursor=previous-page&limit=1&ids=1%2C2", null);
        $this->assertTrue(count($found) == 1 && $found[0] instanceof AiKnowledgeBase && $cursor == "next-page");
    }

    public function knowledgeBasePageOfTheLastPageHasANullCursor()
    {
        Request::$answers = [["cursor" => null, "knowledgeBases" => [$this->knowledgeBaseJson]]];

        list($found, $cursor) = AiKnowledgeBase::page();

        $this->assertTrue(count($found) == 1 && is_null($cursor));
    }

    public function knowledgeBaseUpdateSendsOnlyTheGivenFields()
    {
        Request::$answers = [["knowledgeBase" => $this->knowledgeBaseJson]];

        AiKnowledgeBase::update("6767676767676767", ["name" => "Renamed"]);

        $this->assertSent("PATCH", "ai-knowledge-base/6767676767676767", ["name" => "Renamed"]);
    }

    public function knowledgeBaseUpdateWithoutFieldsSendsAnEmptyJsonObject()
    {
        Request::$answers = [["knowledgeBase" => $this->knowledgeBaseJson]];

        AiKnowledgeBase::update("6767676767676767");

        $this->assertSentJson("{}");
    }

    public function knowledgeBaseHostsGroupsPagesByHost()
    {
        $hosts = [
            "docs.starkinfra.com" => [[
                "originalUrl" => "https://docs.starkinfra.com/get-started",
                "status" => "success",
                "storageUrl" => "https://storage.googleapis.com/ai-knowledge/6767676767676767/get-started.md"
            ]]
        ];
        Request::$answers = [["hosts" => $hosts]];

        $result = AiKnowledgeBase::hosts("6767676767676767");

        $this->assertSent("GET", "ai-knowledge-base/6767676767676767/hosts", null);
        $this->assertTrue($result == $hosts);
    }

    public function knowledgeBaseDeleteSendsIdsInTheQueryString()
    {
        Request::$answers = [["knowledgeBases" => [$this->knowledgeBaseJson]]];

        $deleted = AiKnowledgeBase::delete(["6767676767676767", "6767676767676768"]);

        $this->assertSent("DELETE", "ai-knowledge-base?ids=6767676767676767%2C6767676767676768", null);
        $this->assertTrue(count($deleted) == 1 && $deleted[0] instanceof AiKnowledgeBase && $deleted[0]->id == "6767676767676767");
    }

    public function run($name)
    {
        Request::$sent = [];
        Request::$answers = [];
        $this->$name();
    }

    private function entities($count)
    {
        $entities = [];
        for ($index = 0; $index < $count; $index++) {
            $entities[] = ["id" => strval($index)];
        }
        return $entities;
    }

    private function assertSent($method, $url, $payload)
    {
        $this->assertSentAt(count(Request::$sent) - 1, $method, $url, $payload);
    }

    private function assertSentAt($index, $method, $url, $payload)
    {
        $sent = Request::$sent[$index];
        if ($sent["method"] != $method || $sent["url"] !== $url || $sent["payload"] !== $payload) {
            throw new Exception("failed: " . json_encode($sent));
        }
    }

    private function assertSentJson($json)
    {
        $sent = Request::$sent[count(Request::$sent) - 1];
        if ($sent["payloadJson"] !== $json) {
            throw new Exception("failed: " . json_encode($sent));
        }
    }

    private function assertTrue($condition)
    {
        if (!$condition) {
            throw new Exception("failed: " . json_encode(Request::$sent));
        }
    }
}

list($privateKey, $publicKey) = Key::create();
Settings::setUser(new Project([
    "environment" => "sandbox",
    "id" => "1",
    "privateKey" => $privateKey
]));

echo "\n\nAI resources at the HTTP boundary:";

$test = new TestAiAtTheHttpBoundary();
foreach (get_class_methods($test) as $name) {
    if ($name == "run") {
        continue;
    }
    echo "\n\t- " . strtolower(preg_replace("/([A-Z])/", " $1", $name));
    $test->run($name);
    echo " - OK";
}
