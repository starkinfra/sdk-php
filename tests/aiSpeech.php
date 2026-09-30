<?php

namespace Test\AiSpeech;
use \Exception;
use StarkInfra\AiSpeech;
use StarkInfra\AiVoice;
use StarkCore\Error\InputErrors;


class TestAiSpeech
{
    public function create()
    {
        foreach (AiVoice::query() as $voice) {
            if ($voice->status != "success") {
                continue;
            }
            $speech = AiSpeech::create(new AiSpeech(["voiceId" => $voice->id, "text" => "Short test."]));

            if (!is_string($speech->id) || $speech->status != "success" || !is_string($speech->audio) || $speech->audio == "") {
                throw new Exception("failed");
            }
            return true;
        }
        return false;
    }

    public function queryLeavesTheAudioOut()
    {
        foreach (AiSpeech::query(["limit" => 5]) as $speech) {
            if (!is_string($speech->id) || !is_null($speech->audio)) {
                throw new Exception("failed");
            }
        }
    }

    public function queryWithExpandVoiceName()
    {
        foreach (AiSpeech::query(["expand" => ["voiceName"], "limit" => 5]) as $speech) {
            if (!is_string($speech->voiceName)) {
                throw new Exception("failed");
            }
        }
    }

    public function queryWithLimitStopsAtTheLimit()
    {
        $found = iterator_to_array(AiSpeech::query(["limit" => 2]), false);

        if (count($found) > 2) {
            throw new Exception("failed");
        }
    }

    public function pageReturnsSpeechesWithoutAudio()
    {
        list($speeches) = AiSpeech::page(["limit" => 5]);

        foreach ($speeches as $speech) {
            if (!($speech instanceof AiSpeech) || !is_null($speech->audio)) {
                throw new Exception("failed");
            }
        }
    }

    public function getReturnsTheAudio()
    {
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
        throw new Exception("no finished speech found");
    }

    public function queryWithInvalidLimit()
    {
        $error = false;
        try {
            iterator_to_array(AiSpeech::query(["limit" => -1]));
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
            AiSpeech::page(["limit" => 101]);
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
            AiSpeech::get("0000000000000000");
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nAiSpeech:";

$test = new TestAiSpeech();

echo "\n\t- create reads the text out loud";
echo $test->create() ? " - OK" : " - SKIPPED (no voice able to speak in the sandbox)";

echo "\n\t- query leaves the audio out";
$test->queryLeavesTheAudioOut();
echo " - OK";

echo "\n\t- query with expand voiceName";
$test->queryWithExpandVoiceName();
echo " - OK";

echo "\n\t- query with limit stops at the limit";
$test->queryWithLimitStopsAtTheLimit();
echo " - OK";

echo "\n\t- page returns speeches without audio";
$test->pageReturnsSpeechesWithoutAudio();
echo " - OK";

echo "\n\t- get returns the audio";
$test->getReturnsTheAudio();
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
