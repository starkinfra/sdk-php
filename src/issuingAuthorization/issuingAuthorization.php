<?php

namespace StarkInfra;
use \Exception;
use EllipticCurve\PublicKey;
use EllipticCurve\Signature;
use EllipticCurve\Ecdsa;
use StarkInfra\Error\InvalidSignatureError;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\API;
use StarkInfra\Utils\Request;
use StarkInfra\Utils\Cache;
use StarkInfra\Utils\Parse;


class IssuingAuthorization extends Resource
{
    /**
    # Webhook IssuingAuthorization object

    An IssuingAuthorization is the received purchase data to be analysed and answered with the approval or decline.

    ## Attributes:
        - endToEndId [string, default null]: unique purchase identifier in the Stark system. ex: "E79457883202101262140HHX553UPqeq"
        - amount [integer, default null]: IssuingPurchase value in cents. Minimum = 0 (any value will be accepted). ex: 1234 (= R$ 12.34)
        - tax [integer, default 0]: IOF amount taxed for international purchases. ex: 1234 (= R$ 12.34)
        - cardId [string, default null]: unique id returned when IssuingCard is created. ex: "5656565656565656"
        - issuerAmount [integer, default null]: issuer amount. ex: 1234 (= R$ 12.34)
        - issuerCurrencyCode [string, default null]: issuer currency code. ex: "USD"
        - merchantAmount [integer, default null]: merchant amount. ex: 1234 (= R$ 12.34)
        - merchantCurrencyCode [string, default null]: merchant currency code. ex: "USD"
        - merchantCategoryCode [string, default null]: merchant category code. ex: "eatingPlacesRestaurants"
        - merchantCountryCode [string, default null]: merchant country code. ex: "USA"
        - acquirerId [string, default null]: acquirer ID. ex: "5656565656565656"
        - merchantId [string, default null]: merchant ID. ex: "5656565656565656"
        - merchantName [string, default null]: merchant name. ex: "Google Cloud Platform"
        - merchantFee [integer, default null]: merchant fee charged. ex: 200 (= R$ 2.00)
        - walletId [string, default null]: virtual wallet ID. ex: "5656565656565656"
        - methodCode [string, default null]: method code. ex: "chip", "token", "server", "manual", "magstripe" or "contactless"
        - score [float, default 0.0]: internal score calculated for the authenticity of the purchase. ex: 7.6
        - isPartialAllowed [bool, default False]: true if the the merchant allows partial purchases. ex: False
        - purpose [string, default null]: purchase purpose. ex: "purchase"
        - cardTags [list of strings, default null]: list of tags of the IssuingCard. ex: ["travel", "food"]
        - holderTags [list of strings, default null]: list of tags of the IssuingHolder. ex: ["travel", "food"]
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->endToEndId = Checks::checkParam($params, "endToEndId");
        $this->amount = Checks::checkParam($params, "amount");
        $this->tax = Checks::checkParam($params, "tax");
        $this->cardId = Checks::checkParam($params, "cardId");
        $this->issuerAmount = Checks::checkParam($params, "issuerAmount");
        $this->issuerCurrencyCode = Checks::checkParam($params, "issuerCurrencyCode");
        $this->merchantAmount = Checks::checkParam($params, "merchantAmount");
        $this->merchantCurrencyCode = Checks::checkParam($params, "merchantCurrencyCode");
        $this->merchantCategoryCode = Checks::checkParam($params, "merchantCategoryCode");
        $this->merchantCountryCode = Checks::checkParam($params, "merchantCountryCode");
        $this->acquirerId = Checks::checkParam($params, "acquirerId");
        $this->merchantId = Checks::checkParam($params, "merchantId");
        $this->merchantName = Checks::checkParam($params, "merchantName");
        $this->merchantFee = Checks::checkParam($params, "merchantFee");
        $this->walletId = Checks::checkParam($params, "walletId");
        $this->methodCode = Checks::checkParam($params, "methodCode");
        $this->score = Checks::checkParam($params, "score");
        $this->isPartialAllowed = Checks::checkParam($params, "isPartialAllowed");
        $this->purpose = Checks::checkParam($params, "purpose");
        $this->cardTags = Checks::checkParam($params, "cardTags");
        $this->holderTags = Checks::checkParam($params, "holderTags");

        Checks::checkParams($params);
    }

    /**
    # Create single IssuingAuthorization from a content string

    Create a single IssuingAuthorization object received from IssuingAuthorization at the informed endpoint.
    If the provided digital signature does not check out with the Stark public key, a
    starkinfra.exception.InvalidSignatureException will be raised.

    ## Parameters (required):
        - content [string]: response content from request received at user endpoint (not parsed)
        - signature [string]: base-64 digital signature received at response header "Digital-Signature"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingAuthorization object
     */
    public static function parse($content, $signature, $user = null)
    {
        return Parse::parseAndVerify($content, $signature, IssuingAuthorization::resource(), $user);
    }

    private static function resource()
    {
        $authorization = function ($array) {
            return new IssuingAuthorization($array);
        };
        return [
            "name" => "IssuingAuthorization",
            "maker" => $authorization,
        ];
    }
}
