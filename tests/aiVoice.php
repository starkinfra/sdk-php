<?php

namespace Test\AiVoice;
use Exception;
use StarkInfra\AiVoice;
use Test\Utils\AiFixtures;

include_once("tests/utils/aiFixtures.php");


echo "\n\nAiVoice:";

AiFixtures::run("create clones a recording", function () {
    $voice = AiFixtures::voice();
    if (is_null($voice)) {
        return false;
    }

    if (!is_string($voice->id) || $voice->status != "processing" || !($voice->created instanceof \DateTime)) {
        throw new Exception("failed");
    }
    if (!AiFixtures::pagesContain("StarkInfra\AiVoice", $voice->id)) {
        throw new Exception("failed");
    }
});

AiFixtures::run("query", function () {
    foreach (AiVoice::query(["limit" => 5]) as $voice) {
        if (!($voice instanceof AiVoice) || !is_string($voice->id) || $voice->audio !== null) {
            throw new Exception("failed");
        }
    }
});

AiFixtures::run("delete returns the deleted voices", function () {
    $audio = AiFixtures::audio();
    if (is_null($audio)) {
        return false;
    }
    $voice = AiVoice::create(new AiVoice(["audio" => $audio, "name" => "sdk-php-delete"]));

    $deleted = AiVoice::delete([$voice->id]);

    if (count($deleted) != 1 || $deleted[0]->id != $voice->id) {
        throw new Exception("failed");
    }
});

AiFixtures::run("page with an invalid limit raises input errors", function () {
    AiFixtures::assertPageLimitRaises("StarkInfra\AiVoice", 101);
    AiFixtures::assertPageLimitRaises("StarkInfra\AiVoice", 0);
});
