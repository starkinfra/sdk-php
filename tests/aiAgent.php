<?php

namespace Test\AiAgent;
use Exception;
use StarkInfra\AiAgent;
use StarkInfra\AiKnowledgeBase;
use Test\Utils\AiFixtures;

include_once("tests/utils/aiFixtures.php");


echo "\n\nAiAgent:";

AiFixtures::run("create keeps the schema keys as written", function () {
    $agent = AiFixtures::agent();

    if (!is_string($agent->id) || $agent->model != "bender-1.0" || !($agent->created instanceof \DateTime)) {
        throw new Exception("failed");
    }
    if ($agent->knowledgeBaseIds != [AiFixtures::knowledgeBase()->id] || array_keys($agent->metadataSchema) != ["order_id"]) {
        throw new Exception("failed");
    }
});

AiFixtures::run("get and expand knowledge bases", function () {
    $agent = AiFixtures::agent();

    $plain = AiAgent::get($agent->id);
    if ($plain->id != $agent->id || !is_null($plain->knowledgeBases)) {
        throw new Exception("failed");
    }
    $expanded = AiAgent::get($agent->id, ["expand" => ["knowledgeBases"]]);
    if (count($expanded->knowledgeBases) != 1 || !($expanded->knowledgeBases[0] instanceof AiKnowledgeBase)) {
        throw new Exception("failed");
    }
    if ($expanded->knowledgeBases[0]->id != AiFixtures::knowledgeBase()->id) {
        throw new Exception("failed");
    }
});

AiFixtures::run("query with expand", function () {
    $agent = AiFixtures::agent();

    foreach (AiAgent::query(["expand" => ["knowledgeBases"]]) as $found) {
        if ($found->id != $agent->id) {
            continue;
        }
        if (count($found->knowledgeBases) != 1) {
            throw new Exception("failed");
        }
        return;
    }
    throw new Exception("failed");
});

AiFixtures::run("page leads to the created agent", function () {
    if (!AiFixtures::pagesContain("StarkInfra\AiAgent", AiFixtures::agent()->id)) {
        throw new Exception("failed");
    }
});

AiFixtures::run("update keeps what it was not asked to change", function () {
    $agent = AiFixtures::agent();
    try {
        $renamed = AiAgent::update($agent->id, ["name" => "renamed-by-sdk"]);

        if ($renamed->name != "renamed-by-sdk" || $renamed->knowledgeBaseIds != [AiFixtures::knowledgeBase()->id]) {
            throw new Exception("failed");
        }
        if (array_keys($renamed->metadataSchema) != ["order_id"] || $renamed->systemPrompt != $agent->systemPrompt) {
            throw new Exception("failed");
        }
    } finally {
        AiAgent::update($agent->id, ["name" => $agent->name]);
    }
});

AiFixtures::run("update with empty values clears the fields", function () {
    $agent = AiAgent::create(AiFixtures::generateExampleAiAgent([AiFixtures::knowledgeBase()->id]));
    try {
        $cleared = AiAgent::update($agent->id, ["systemPrompt" => "", "knowledgeBaseIds" => [], "metadataSchema" => []]);

        if ($cleared->knowledgeBaseIds !== [] || $cleared->metadataSchema != [] || $cleared->systemPrompt != "") {
            throw new Exception("failed");
        }
    } finally {
        AiAgent::delete([$agent->id]);
    }
});

AiFixtures::run("delete returns the deleted agents", function () {
    $agent = AiAgent::create(AiFixtures::generateExampleAiAgent());

    $deleted = AiAgent::delete([$agent->id]);

    if (count($deleted) != 1 || $deleted[0]->id != $agent->id) {
        throw new Exception("failed");
    }
});

AiFixtures::run("page with an invalid limit raises input errors", function () {
    AiFixtures::assertPageLimitRaises("StarkInfra\AiAgent", 101);
    AiFixtures::assertPageLimitRaises("StarkInfra\AiAgent", 0);
});

AiFixtures::run("create with an invalid model raises input errors", function () {
    AiFixtures::assertRaises("StarkCore\Error\InputErrors", function () {
        AiAgent::create(new AiAgent(["name" => "invalid", "model" => "gpt"]));
    });
});

AiFixtures::run("get unknown id raises input errors", function () {
    AiFixtures::assertRaises("StarkCore\Error\InputErrors", function () {
        AiAgent::get("0000000000000000");
    });
});
