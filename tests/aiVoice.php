<?php

namespace Test\AiVoice;
use \Exception;
use StarkInfra\AiVoice;
use Test\Utils\AudioExample;
use StarkCore\Error\InputErrors;

include_once("tests/utils/aiAudio.php");


class TestAiVoice
{
    public $voice;

    public function create()
    {
        $this->voice = AiVoice::create(new AiVoice([
            "audio" => AudioExample::generateExampleAudio(),
            "name" => "sdk-php-voice-" . bin2hex(random_bytes(6))
        ]));

        if (!is_string($this->voice->id) || $this->voice->status != "processing" || !($this->voice->created instanceof \DateTime)) {
            throw new Exception("failed");
        }
    }

    public function query()
    {
        foreach (AiVoice::query(["limit" => 5]) as $voice) {
            if (!is_string($voice->id) || !is_null($voice->audio)) {
                throw new Exception("failed");
            }
        }
    }

    public function pageLeadsToTheCreatedVoice()
    {
        $options = ["limit" => 100];
        do {
            list($found, $cursor) = AiVoice::page($options);
            foreach ($found as $voice) {
                if ($voice->id == $this->voice->id) {
                    return;
                }
            }
            $options["cursor"] = $cursor;
        } while (!is_null($cursor));
        throw new Exception("failed");
    }

    public function delete()
    {
        $deleted = AiVoice::delete([$this->voice->id]);

        if (count($deleted) != 1 || $deleted[0]->id != $this->voice->id) {
            throw new Exception("failed");
        }
    }

    public function queryWithInvalidLimit()
    {
        $error = false;
        try {
            iterator_to_array(AiVoice::query(["limit" => -1]));
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
            AiVoice::page(["limit" => 101]);
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nAiVoice:";

$test = new TestAiVoice();

echo "\n\t- create clones a recording";
$test->create();
echo " - OK";

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- page leads to the created voice";
$test->pageLeadsToTheCreatedVoice();
echo " - OK";

echo "\n\t- delete returns the deleted voices";
$test->delete();
echo " - OK";

echo "\n\t- query with invalid limit";
$test->queryWithInvalidLimit();
echo " - OK";

echo "\n\t- page with invalid limit";
$test->pageWithInvalidLimit();
echo " - OK";
