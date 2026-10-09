<?php

namespace Test\AiSpeech;
use Exception;
use StarkInfra\AiSpeech;
use Test\Utils\AiFixtures;

include_once("tests/utils/aiFixtures.php");


echo "\n\nAiSpeech:";

AiFixtures::run("create reads the text out loud with a voice that can speak", function () {
    $voice = AiFixtures::speakingVoice();
    if (is_null($voice)) {
        return false;
    }

    $speech = AiSpeech::create(new AiSpeech(["voiceId" => $voice->id, "text" => "Short test."]));

    if (!is_string($speech->id) || $speech->status != "success" || !is_string($speech->audio) || $speech->audio == "") {
        throw new Exception("failed");
    }
    if (!AiFixtures::pagesContain("StarkInfra\AiSpeech", $speech->id)) {
        throw new Exception("failed");
    }
});

AiFixtures::run("query leaves the audio out", function () {
    foreach (AiSpeech::query(["limit" => 5]) as $speech) {
        if (!is_string($speech->id) || !is_null($speech->audio)) {
            throw new Exception("failed");
        }
    }
});

AiFixtures::run("query with expand voiceName", function () {
    foreach (AiSpeech::query(["expand" => ["voiceName"], "limit" => 5]) as $speech) {
        if (!is_string($speech->voiceName)) {
            throw new Exception("failed");
        }
    }
});

AiFixtures::run("query with limit stops at the limit", function () {
    $found = iterator_to_array(AiSpeech::query(["limit" => 2]), false);

    if (count($found) > 2) {
        throw new Exception("failed");
    }
});

AiFixtures::run("page returns the speeches and a cursor that is null on the last page", function () {
    $options = ["limit" => 100];
    do {
        list($speeches, $cursor) = AiSpeech::page($options);
        foreach ($speeches as $speech) {
            if (!($speech instanceof AiSpeech) || !is_null($speech->audio)) {
                throw new Exception("failed");
            }
        }
        $options["cursor"] = $cursor;
    } while (!is_null($cursor));
});

AiFixtures::run("get returns the audio of a finished speech", function () {
    foreach (AiSpeech::query() as $finished) {
        if ($finished->status != "success") {
            continue;
        }
        $speech = AiSpeech::get($finished->id, ["expand" => ["voiceName"]]);
        if ($speech->id != $finished->id || !is_string($speech->audio) || $speech->audio == "" || !is_string($speech->voiceName)) {
            throw new Exception("failed");
        }
        return;
    }
    return false;
});

AiFixtures::run("page with an invalid limit raises input errors", function () {
    AiFixtures::assertPageLimitRaises("StarkInfra\AiSpeech", 101);
    AiFixtures::assertPageLimitRaises("StarkInfra\AiSpeech", 0);
});

AiFixtures::run("get unknown id raises input errors", function () {
    AiFixtures::assertRaises("StarkCore\Error\InputErrors", function () {
        AiSpeech::get("0000000000000000");
    });
});
