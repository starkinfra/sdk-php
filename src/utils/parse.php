<?php

namespace StarkInfra\Utils;
use StarkInfra\Settings;


class Parse
{
    public static function parseAndVerify($content, $signature, $resource, $user)
    {
        return \StarkCore\Utils\Parse::parseAndVerify(
            $content,
            $signature,
            Settings::getSdkVersion(),
            Settings::getApiVersion(),
            Settings::getHost(),
            $resource,
            Settings::getUser($user),
            Settings::getLanguage(),
            Settings::getTimeout()
        );
    }

    public static function verify($content, $signature, $user = null)
    {
        return \StarkCore\Utils\Parse::verify(
            $content,
            $signature,
            Settings::getSdkVersion(),
            Settings::getApiVersion(),
            Settings::getHost(),
            Settings::getUser($user),
            Settings::getLanguage(),
            Settings::getTimeout()
        );
    }
}
