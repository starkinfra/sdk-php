<?php

namespace StarkInfra;
use StarkInfra\Utils\API;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Parse;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\StarkDate;


class IssuingPurchase extends Resource
{
    /**
    # IssuingPurchase object

    Displays the IssuingPurchase objects created to your Workspace.

    ## Attributes (return-only):
        - id [string]: unique id returned when IssuingPurchase is created. ex: "5656565656565656"
        - holderName [string]: card holder name. ex: "Tony Stark"
        - cardId [string]: unique id returned when IssuingCard is created. ex: "5656565656565656"
        - cardEnding [string]: last 4 digits of the card number. ex: "1234"
        - purpose [string]: purchase purpose. ex: "purchase"
        - amount [integer]: IssuingPurchase value in cents. Minimum = 0. ex: 1234 (= R$ 12.34)
        - tax [integer]: IOF amount taxed for international purchases. ex: 1234 (= R$ 12.34)
        - issuerAmount [integer]: issuer amount. ex: 1234 (= R$ 12.34)
        - issuerCurrencyCode [string]: issuer currency code. ex: "USD"
        - issuerCurrencySymbol [string]: issuer currency symbol. ex: "$"
        - merchantAmount [integer]: merchant amount. ex: 1234 (= R$ 12.34)
        - merchantCurrencyCode [string]: merchant currency code. ex: "USD"
        - merchantCurrencySymbol [string]: merchant currency symbol. ex: "$"
        - merchantCategoryCode [string]: merchant category code. ex: "fastFoodRestaurants"
        - merchantCountryCode [string]: merchant country code. ex: "USA"
        - acquirerId [string]: acquirer ID. ex: "5656565656565656"
        - merchantId [string]: merchant ID. ex: "5656565656565656"
        - merchantName [string]: merchant name. ex: "Google Cloud Platform"
        - merchantFee [integer]: fee charged by the merchant to cover specific costs, such as ATM withdrawal logistics, etc. ex: 200 (= R$ 2.00)
        - walletId [string]: virtual wallet ID. ex: "5656565656565656"
        - methodCode [string]: method code. Options: "chip", "token", "server", "manual" or "contactless"
        - score [float]: internal score calculated for the authenticity of the purchase. null in case of insufficient data. ex: 7.6
        - endToEndId [string]: Unique id used to identify the transaction through all of its life cycle, even before the purchase is denied or accepted and gets its usual id. ex: "679cd385-642b-49d0-96b7-89491e1249a5"
        - tags [array of string]: array of strings for tagging returned by the sub-issuer during the authorization. ex: ["travel", "food"]

    ## Attributes (IssuingPurchase only):
        - issuingTransactionIds [string]: ledger transaction ids linked to this Purchase
        - status [string]: current IssuingCard status. Options: "approved", "canceled", "denied", "confirmed", "voided"
        - updated [Date]: latest update datetime for the IssuingPurchase.
        - created [Date]: creation datetime for the IssuingPurchase.
        
    ## Attributes (authorization request only):
        - isPartialAllowed [bool]: true if the the merchant allows partial purchases. ex: False
        - cardTags [array of strings]: tags of the IssuingCard responsible for this purchase. ex: ["travel", "food"]
        - holderTags [array of strings]: tags of the IssuingHolder responsible for this purchase. ex: ["technology", "john snow"]
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->holderName = Checks::checkParam($params, "holderName");
        $this->cardId = Checks::checkParam($params, "cardId");
        $this->cardEnding = Checks::checkParam($params, "cardEnding");
        $this->purpose = Checks::checkParam($params, "purpose");
        $this->amount = Checks::checkParam($params, "amount");
        $this->tax = Checks::checkParam($params, "tax");
        $this->issuerAmount = Checks::checkParam($params, "issuerAmount");
        $this->issuerCurrencyCode = Checks::checkParam($params, "issuerCurrencyCode");
        $this->issuerCurrencySymbol = Checks::checkParam($params, "issuerCurrencySymbol");
        $this->merchantAmount = Checks::checkParam($params, "merchantAmount");
        $this->merchantCurrencyCode = Checks::checkParam($params, "merchantCurrencyCode");
        $this->merchantCurrencySymbol = Checks::checkParam($params, "merchantCurrencySymbol");
        $this->merchantCategoryCode = Checks::checkParam($params, "merchantCategoryCode");
        $this->merchantCountryCode = Checks::checkParam($params, "merchantCountryCode");
        $this->acquirerId = Checks::checkParam($params, "acquirerId");
        $this->merchantId = Checks::checkParam($params, "merchantId");
        $this->merchantName = Checks::checkParam($params, "merchantName");
        $this->merchantFee = Checks::checkParam($params, "merchantFee");
        $this->walletId = Checks::checkParam($params, "walletId");
        $this->methodCode = Checks::checkParam($params, "methodCode");
        $this->score = Checks::checkParam($params, "score");
        $this->endToEndId = Checks::checkParam($params, "endToEndId");
        $this->tags = Checks::checkParam($params, "tags");
        $this->issuingTransactionIds = Checks::checkParam($params, "issuingTransactionIds");
        $this->status = Checks::checkParam($params, "status");
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->isPartialAllowed = Checks::checkParam($params, "isPartialAllowed");
        $this->cardTags = Checks::checkParam($params, "cardTags");
        $this->holderTags = Checks::checkParam($params, "holderTags");
        
        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific IssuingPurchase

    Receive a single IssuingPurchase object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingPurchase object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IssuingPurchase::resource(), $id);
    }

    /**
    # Retrieve IssuingPurchases

    Receive an enumerator of IssuingPurchase objects previously created in the Stark Infra API

    ## Parameters (optional):
        - endToEndIds [array of strings, default []]: central bank's unique transaction ID. ex: "E79457883202101262140HHX553UPqeq"
        - holderIds [array of strings, default []]: card holder IDs. ex: ["5656565656565656", "4545454545454545"]
        - cardIds [array of strings, default []]: card  IDs. ex: ["5656565656565656", "4545454545454545"]
        - status [string, default null]: filter for status of retrieved objects. ex: "approved", "canceled", "denied", "confirmed" or "voided"
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - ids [array of strings, default [], default null]: purchase IDs
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingPurchase objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, IssuingPurchase::resource(), $options);
    }

    /**
    # Retrieve paged Purchases

    Receive a list of up to 100 Purchase objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - endToEndIds [array of strings, default []]: central bank's unique transaction ID. ex: "E79457883202101262140HHX553UPqeq"
        - holderIds [array of strings, default []]: card holder IDs. ex: ["5656565656565656", "4545454545454545"]
        - cardIds [array of strings, default []]: card  IDs. ex: ["5656565656565656", "4545454545454545"]
        - status [string, default null]: filter for status of retrieved objects. ex: "approved", "canceled", "denied", "confirmed" or "voided"
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - ids [array of strings, default [], default null]: purchase IDs
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call

    ## Return:
        - list of IssuingPurchase objects with updated attributes
        - cursor to retrieve the next page of IssuingPurchase objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, IssuingPurchase::resource(), $options);
    }

    /**
    # Create a IssuingPurchase object from a content string

    Create a single IssuingPurchase object from a content string received from a POST 
    request to your registered URL.
    If the provided digital signature does not check out with the Stark public key, a
    StarkInfra\Exception\InvalidSignatureException will be raised.

    ## Parameters (required):
        - content [string]: response content from request received at user endpoint (not parsed)
        - signature [string]: base-64 digital signature received at response header "Digital-Signature"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - Parsed IssuingPurchase object
     */
    public static function parse($content, $signature, $user = null)
    {
        return Parse::parseAndVerify($content, $signature, IssuingPurchase::resource(), $user);
    }

    /** 
    # Helps you respond to an IssuingPurchase authorization request.

    ## Parameters (required):
        - status [string]: sub-issuer response to the authorization. ex: "approved" or "denied"
    
    ## Parameters (optional):
        - amount [integer, default null]: amount in cents that was authorized. ex: 1234 (= R$ 12.34)
        - reason [string, default null]: denial reason. Options: "other", "blocked", "lostCard", "stolenCard", "invalidPin", "invalidCard", "cardExpired", "issuerError", "concurrency", "standInDenial", "subIssuerError", "invalidPurpose", "invalidZipCode", "invalidWalletId", "inconsistentCard", "settlementFailed", "cardRuleMismatch", "invalidExpiration", "prepaidInstallment", "holderRuleMismatch", "insufficientBalance", "tooManyTransactions", "invalidSecurityCode", "invalidPaymentMethod", "confirmationDeadline", "withdrawalAmountLimit", "insufficientCardLimit", "insufficientHolderLimit"
        - tags [array of strings, default null]: tags to filter retrieved object. ex: ["tony", "stark"]

    ## Return:
        - Dumped JSON string that must be returned to us on the IssuingPurchase authorization response
    */
    public static function response($params)
    {
        $params = ([
            "authorization" => [
                "status" => Checks::checkParam($params, "status"),
                "amount" => Checks::checkParam($params, "amount"),
                "reason" => Checks::checkParam($params, "reason"),
                "tags" => Checks::checkParam($params, "tags"),
            ]
        ]);
        return json_encode(API::apiJson($params));
    }

    private static function resource()
    {
        $purchase = function ($array) {
            return new IssuingPurchase($array);
        };
        return [
            "name" => "IssuingPurchase",
            "maker" => $purchase,
        ];
    }
}
