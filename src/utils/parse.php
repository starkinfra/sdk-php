<?php

namespace StarkInfra\Utils;
use StarkInfra\Settings;
use StarkInfra\Utils\Rest;


class Parse
{
    public static function parseAndVerify($content, $signature, $resource, $user)
    {
        return \StarkCore\Utils\Parse::parseAndVerify(
            $content,
            $signature,
            Rest::getSdkVersion(),
            Rest::getApiVersion(),
            Rest::getHost(),
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
            Rest::getSdkVersion(),
            Rest::getApiVersion(),
            Rest::getHost(),
            Settings::getUser($user),
            Settings::getLanguage(),
            Settings::getTimeout()
        );
    }
}
