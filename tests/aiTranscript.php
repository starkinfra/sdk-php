<?php

namespace Test\AiTranscript;
use \Exception;
use StarkInfra\AiTranscript;
use Test\Utils\AudioExample;
use StarkCore\Error\InputErrors;

include_once("tests/utils/aiAudio.php");


class TestAiTranscript
{
    public $transcript;

    public function create()
    {
        $this->transcript = AiTranscript::create(new AiTranscript(["audio" => AudioExample::generateExampleAudio()]));

        if (!is_string($this->transcript->id) || $this->transcript->status != "success" || !is_string($this->transcript->text)) {
            throw new Exception("failed");
        }
    }

    public function query()
    {
        foreach (AiTranscript::query(["limit" => 5]) as $transcript) {
            if (!is_string($transcript->id) || !is_null($transcript->audio)) {
                throw new Exception("failed");
            }
        }
    }

    public function pageLeadsToTheCreatedTranscript()
    {
        $options = ["limit" => 100];
        do {
            list($found, $cursor) = AiTranscript::page($options);
            foreach ($found as $transcript) {
                if ($transcript->id == $this->transcript->id) {
                    return;
                }
            }
            $options["cursor"] = $cursor;
        } while (!is_null($cursor));
        throw new Exception("failed");
    }

    public function queryWithInvalidLimit()
    {
        $error = false;
        try {
            iterator_to_array(AiTranscript::query(["limit" => -1]));
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
            AiTranscript::page(["limit" => 101]);
        } catch (InputErrors $e) {
            $error = true;
        }
        if (!$error) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nAiTranscript:";

$test = new TestAiTranscript();

echo "\n\t- create transcribes a recording";
$test->create();
echo " - OK";

echo "\n\t- query";
$test->query();
echo " - OK";

echo "\n\t- page leads to the created transcript";
$test->pageLeadsToTheCreatedTranscript();
echo " - OK";

echo "\n\t- query with invalid limit";
$test->queryWithInvalidLimit();
echo " - OK";

echo "\n\t- page with invalid limit";
$test->pageWithInvalidLimit();
echo " - OK";
