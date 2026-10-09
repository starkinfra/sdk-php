<?php

namespace Test\AiMessage;
use Exception;
use StarkInfra\AiMessage;
use Test\Utils\AiFixtures;

include_once("tests/utils/aiFixtures.php");


function ids($messages)
{
    $ids = [];
    foreach ($messages as $message) {
        $ids[] = $message->id;
    }
    sort($ids);
    return $ids;
}

echo "\n\nAiMessage:";

AiFixtures::run("create returns the user message and the answer", function () {
    $posted = AiFixtures::posted();

    if (count($posted) != 2 || $posted[0]->sender != "user" || $posted[1]->sender != "system") {
        throw new Exception("failed");
    }
    foreach ($posted as $message) {
        if ($message->chatId != AiFixtures::chat()->id || !($message->created instanceof \DateTime) || !is_string($message->chatName) || $message->chatName == "") {
            throw new Exception("failed");
        }
    }
});

AiFixtures::run("the answer carries a metadata array", function () {
    $posted = AiFixtures::posted();

    if (!is_array($posted[1]->metadata)) {
        throw new Exception("failed");
    }
});

AiFixtures::run("query returns the whole history of the chat", function () {
    $posted = AiFixtures::posted();

    $found = iterator_to_array(AiMessage::query(["chatId" => AiFixtures::chat()->id]), false);

    if (ids($found) != ids($posted)) {
        throw new Exception("failed");
    }
});

AiFixtures::run("query with limit stops at the limit", function () {
    AiFixtures::posted();

    $found = iterator_to_array(AiMessage::query(["chatId" => AiFixtures::chat()->id, "limit" => 1]), false);

    if (count($found) != 1) {
        throw new Exception("failed");
    }
});

AiFixtures::run("query without a chat id reads the workspace history", function () {
    AiFixtures::posted();

    $found = iterator_to_array(AiMessage::query(["limit" => 1]), false);

    if (count($found) != 1 || !($found[0] instanceof AiMessage)) {
        throw new Exception("failed");
    }
});

AiFixtures::run("page returns a cursor that leads to the next page", function () {
    AiFixtures::posted();
    $chatId = AiFixtures::chat()->id;

    list($first, $cursor) = AiMessage::page(["chatId" => $chatId, "limit" => 1]);
    if (count($first) != 1 || is_null($cursor)) {
        throw new Exception("failed");
    }
    list($second, $next) = AiMessage::page(["chatId" => $chatId, "cursor" => $cursor, "limit" => 1]);

    if (count($second) != 1 || $second[0]->id == $first[0]->id) {
        throw new Exception("failed");
    }
});

AiFixtures::run("page of the whole history ends with a null cursor", function () {
    $posted = AiFixtures::posted();

    list($messages, $cursor) = AiMessage::page(["chatId" => AiFixtures::chat()->id, "limit" => 100]);

    if (ids($messages) != ids($posted) || !is_null($cursor)) {
        throw new Exception("failed");
    }
});

AiFixtures::run("create round-trips accented and emoji text", function () {
    $text = "Ol\u{e1} \u{2014} \u{201c}x\u{201d} \u{20ac}5 \u{1f600}. Repeat this sentence.";

    $messages = AiMessage::create(new AiMessage(["chatId" => AiFixtures::chat()->id, "text" => $text]));

    if ($messages[0]->text !== $text) {
        throw new Exception("failed");
    }
});

AiFixtures::run("page with an invalid limit raises input errors", function () {
    AiFixtures::assertPageLimitRaises("StarkInfra\AiMessage", 101);
    AiFixtures::assertPageLimitRaises("StarkInfra\AiMessage", 0);
});

AiFixtures::run("create in an unknown chat raises input errors", function () {
    AiFixtures::assertRaises("StarkCore\Error\InputErrors", function () {
        AiMessage::create(new AiMessage(["chatId" => "0000000000000000", "text" => "hi"]));
    });
});
