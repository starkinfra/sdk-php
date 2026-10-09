<?php

namespace Test\AiChat;
use Exception;
use StarkInfra\AiChat;
use Test\Utils\AiFixtures;

include_once("tests/utils/aiFixtures.php");


echo "\n\nAiChat:";

AiFixtures::run("create keeps tags and context", function () {
    $chat = AiFixtures::chat();

    if (!is_string($chat->id) || $chat->agentId != AiFixtures::agent()->id || !($chat->updated instanceof \DateTime)) {
        throw new Exception("failed");
    }
    if ($chat->tags != ["sdk-php", "test"] || $chat->context != ["orderId" => "123", "isUrgent" => true]) {
        throw new Exception("failed");
    }
});

AiFixtures::run("create without optionals returns empty tags and context", function () {
    $chat = AiChat::create(new AiChat(["agentId" => AiFixtures::agent()->id]));
    try {
        if ($chat->tags != [] || $chat->context != []) {
            throw new Exception("failed");
        }
    } finally {
        AiChat::delete([$chat->id]);
    }
});

AiFixtures::run("get with expand agentName", function () {
    $chat = AiFixtures::chat();

    $fetched = AiChat::get($chat->id, ["expand" => ["agentName"]]);

    if ($fetched->id != $chat->id || $fetched->agentName != AiFixtures::agent()->name) {
        throw new Exception("failed");
    }
    if (!is_null(AiChat::get($chat->id)->agentName)) {
        throw new Exception("failed");
    }
});

AiFixtures::run("query filters by tags", function () {
    $chat = AiFixtures::chat();

    foreach (AiChat::query(["tags" => ["sdk-php", "test"], "expand" => ["agentName"]]) as $found) {
        if ($found->id == $chat->id && $found->agentName == AiFixtures::agent()->name) {
            return;
        }
    }
    throw new Exception("failed");
});

AiFixtures::run("query with an unknown tag is empty", function () {
    AiFixtures::chat();

    $found = iterator_to_array(AiChat::query(["tags" => ["no-chat-has-this-tag"]]), false);

    if (count($found) != 0) {
        throw new Exception("failed");
    }
});

AiFixtures::run("page leads to the created chat", function () {
    if (!AiFixtures::pagesContain("StarkInfra\AiChat", AiFixtures::chat()->id)) {
        throw new Exception("failed");
    }
});

AiFixtures::run("update renames the chat and keeps the rest", function () {
    $chat = AiFixtures::chat();
    try {
        $renamed = AiChat::update($chat->id, ["title" => "renamed-by-sdk"]);

        if ($renamed->title != "renamed-by-sdk" || $renamed->agentId != $chat->agentId) {
            throw new Exception("failed");
        }
        if ($renamed->tags != $chat->tags || $renamed->context != $chat->context) {
            throw new Exception("failed");
        }
    } finally {
        AiChat::update($chat->id, ["title" => $chat->title]);
    }
});

AiFixtures::run("update replaces tags and context and clears them with empty values", function () {
    $chat = AiChat::create(AiFixtures::generateExampleAiChat(AiFixtures::agent()->id));
    try {
        $changed = AiChat::update($chat->id, ["tags" => ["changed"], "context" => ["orderId" => "456"]]);
        if ($changed->tags != ["changed"] || $changed->context != ["orderId" => "456"]) {
            throw new Exception("failed");
        }
        $cleared = AiChat::update($chat->id, ["tags" => [], "context" => []]);
        if ($cleared->tags != [] || $cleared->context != []) {
            throw new Exception("failed");
        }
    } finally {
        AiChat::delete([$chat->id]);
    }
});

AiFixtures::run("delete returns the deleted chats", function () {
    $chat = AiChat::create(new AiChat(["agentId" => AiFixtures::agent()->id, "title" => "sdk-php-delete"]));

    $deleted = AiChat::delete([$chat->id]);

    if (count($deleted) != 1 || $deleted[0]->id != $chat->id) {
        throw new Exception("failed");
    }
});

AiFixtures::run("page with an invalid limit raises input errors", function () {
    AiFixtures::assertPageLimitRaises("StarkInfra\AiChat", 101);
    AiFixtures::assertPageLimitRaises("StarkInfra\AiChat", 0);
});

AiFixtures::run("get unknown id raises input errors", function () {
    AiFixtures::assertRaises("StarkCore\Error\InputErrors", function () {
        AiChat::get("0000000000000000");
    });
});
