<?php

namespace Test\AiTranscript;
use Exception;
use StarkInfra\AiTranscript;
use Test\Utils\AiFixtures;

include_once("tests/utils/aiFixtures.php");


echo "\n\nAiTranscript:";

AiFixtures::run("create transcribes a recording", function () {
    $audio = AiFixtures::audio();
    if (is_null($audio)) {
        return false;
    }

    $transcript = AiTranscript::create(new AiTranscript(["audio" => $audio]));

    if (!is_string($transcript->id) || $transcript->status != "success" || !is_string($transcript->text)) {
        throw new Exception("failed");
    }
    if (!AiFixtures::pagesContain("StarkInfra\AiTranscript", $transcript->id)) {
        throw new Exception("failed");
    }
});

AiFixtures::run("query", function () {
    foreach (AiTranscript::query(["limit" => 5]) as $transcript) {
        if (!($transcript instanceof AiTranscript) || !is_string($transcript->id) || !is_null($transcript->audio)) {
            throw new Exception("failed");
        }
    }
});

AiFixtures::run("page ends with a null cursor", function () {
    $options = ["limit" => 100];
    do {
        list($transcripts, $cursor) = AiTranscript::page($options);
        $options["cursor"] = $cursor;
    } while (!is_null($cursor));
});

AiFixtures::run("page with an invalid limit raises input errors", function () {
    AiFixtures::assertPageLimitRaises("StarkInfra\AiTranscript", 101);
    AiFixtures::assertPageLimitRaises("StarkInfra\AiTranscript", 0);
});
