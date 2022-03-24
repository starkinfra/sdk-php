<?php

namespace StarkInfra\Utils;
use \Exception;
use EllipticCurve\Ecdsa;
use EllipticCurve\PublicKey;
use EllipticCurve\Signature;
use StarkInfra\Error\InvalidSignatureError;

class Parse
{
    public static function parseAndVerify($content, $signature, $resource, $user)
    {
        $json = json_decode($content, true);
        $entity = $json;
        if ($resource["name"] == "Event"){
            $entity = $json[API::lastName($resource["name"])];
        }

        try {
            $signature = Signature::fromBase64($signature);
        } catch (Exception $e) {
            throw new InvalidSignatureError("The provided signature is not valid");
        }

        if (self::verifySignature($user, $content, $signature)) {
            return API::fromApiJson($resource["maker"], $entity);
        }
        if (self::verifySignature($user, $content, $signature, true)) {
            return API::fromApiJson($resource["maker"], $entity);
        }

        throw new InvalidSignatureError("The provided signature and content do not match the Stark public key");
    }

    private static function verifySignature($user, $content, $signature, $refresh = false)
    {
        $publicKey = Cache::getStarkPublicKey();
        if (is_null($publicKey) | $refresh) {
            $pem = self::getPublicKeyPem($user);
            $publicKey = PublicKey::fromPem($pem);
            Cache::setStarkPublicKey($publicKey);
        }
        return Ecdsa::verify($content, $signature, $publicKey);
    }

    private static function getPublicKeyPem($user)
    {
        return Request::fetch($user, "GET", "/public-key", null, ["limit" => 1])->json()["publicKeys"][0]["content"];
    }


}

?>