<?php

namespace Test\Utils;
use \Exception;
use StarkInfra\AiSpeech;


class AudioExample
{
    public static function generateExampleAudio()
    {
        foreach (AiSpeech::query() as $speech) {
            if ($speech->status != "success") {
                continue;
            }
            return AiSpeech::get($speech->id)->audio;
        }
        throw new Exception("no finished speech to take the audio from");
    }
}
