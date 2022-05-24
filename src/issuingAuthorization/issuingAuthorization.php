<?php

namespace StarkInfra;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Parse;

class IssuingAuthorization extends Resource
{
    /**
    # IssuingAuthorization object

    An IssuingAuthorization presents purchase data to be analysed and answered with an approval or a declination.

    ## Attributes (return-only):
        - endToEndId [string]: central bank's unique transaction ID. ex: "E79457883202101262140HHX553UPqeq"
        - amount [integer]: IssuingPurchase value in cents. Minimum = 0. ex: 1234 (= R$ 12.34)
        - tax [integer]: IOF amount taxed for international purchases. ex: 1234 (= R$ 12.34)
        - cardId [string]: unique id returned when IssuingCard is created. ex: "5656565656565656"
        - issuerAmount [integer]: issuer amount. ex: 1234 (= R$ 12.34)
        - issuerCurrencyCode [string]: issuer currency code. ex: "USD"
        - merchantAmount [integer]: merchant amount. ex: 1234 (= R$ 12.34)
        - merchantCurrencyCode [string]: merchant currency code. ex: "USD"
        - merchantCategoryCode [string]: merchant category code. ex: "fastFoodRestaurants"
        - merchantCountryCode [string]: merchant country code. ex: "USA"
        - acquirerId [string]: acquirer ID. ex: "5656565656565656"
        - merchantId [string]: merchant ID. ex: "5656565656565656"
        - merchantName [string]: merchant name. ex: "Google Cloud Platform"
        - merchantFee [integer]: merchant fee charged. ex: 200 (= R$ 2.00)
        - walletId [string]: virtual wallet ID. ex: "googlePay"
        - methodCode [string]: method code. ex: "chip", "token", "server", "manual", "magstripe" or "contactless"
        - score [float]: internal score calculated for the authenticity of the purchase. Null in case of insufficient data. ex: 7.6
        - isPartialAllowed [bool]: true if the the merchant allows partial purchases. ex: False
        - purpose [string]: purchase purpose. ex: "purchase"
        - cardTags [array of strings]: tags of the IssuingCard responsible for this purchase. ex: ["travel", "food"]
        - holderTags [array of strings]: tags of the IssuingHolder responsible for this purchase. ex: ["technology", "john snow"]
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
    StarkInfra\Exception\InvalidSignatureException will be raised.

    ## Parameters (required):
        - content [string]: response content from request received at user endpoint (not parsed)
        - signature [string]: base-64 digital signature received at response header "Digital-Signature"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - Parsed IssuingAuthorization object
     */
    public static function parse($content, $signature, $user = null)
    {
        return Parse::parseAndVerify($content, $signature, IssuingAuthorization::resource(), $user);
    }

    /** 
    # Helps you respond IssuingAuthorization requests.

    ## Parameters (required):
        - status [string]: sub-issuer response to the authorization. ex: "accepted" or "denied"
    
    ## Parameters (optional):
        - amount [integer, default 0]: amount in cents that was authorized. ex: 1234 (= R$ 12.34)
        - reason [string, default ""]: denial reason. ex: "other"
        - tags [array of strings, default []]: tags to filter retrieved object. ex: ["tony", "stark"]

    ## Return:
        - Dumped JSON string that must be returned to us on the IssuingAuthorization request
    */
    public static function response($status = null, $amount=null, $reason=null, $tags=null)
    {
        return json_encode([
            "authorization" => [
                "status" => $status,
                "amount" => $amount or 0,
                "reason" => $reason or "",
                "tags" => $tags or [],
            ]
        ]);
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
