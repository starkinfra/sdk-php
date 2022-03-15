<?php

namespace StarkInfra;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\StarkDate;


class IssuingPurchase extends Resource
{
    /**
    # IssuingPurchase object

    Displays the IssuingPurchase objects created to your Workspace.

    ## Attributes (return-only):
        - id [string, default null]: unique id returned when IssuingPurchase is created. ex: "5656565656565656"
        - holderName [string]: card holder name. ex: "Tony Stark"
        - cardId [string, default null]: unique id returned when IssuingCard is created. ex: "5656565656565656"
        - cardEnding [string, default null]: last 4 digits of the card number. ex: "1234"
        - amount [integer, default null]: IssuingPurchase value in cents. Minimum = 0 (any value will be accepted). ex: 1234 (= R$ 12.34)
        - tax [integer, default 0]: IOF amount taxed for international purchases. ex: 1234 (= R$ 12.34)
        - issuerAmount [integer, default null]: issuer amount. ex: 1234 (= R$ 12.34)
        - issuerCurrencyCode [string, default null]: issuer currency code. ex: "USD"
        - issuerCurrencySymbol [string, default null]: issuer currency symbol. ex: "$"
        - merchantAmount [integer, default null]: merchant amount. ex: 1234 (= R$ 12.34)
        - merchantCurrencyCode [string, default null]: merchant currency code. ex: "USD"
        - merchantCurrencySymbol [string, default null]: merchant currency symbol. ex: "$"
        - merchantCategoryCode [string, default null]: merchant category code. ex: "eatingPlacesRestaurants"
        - merchantCountryCode [string, default null]: merchant country code. ex: "USA"
        - acquirerId [string, default null]: acquirer ID. ex: "5656565656565656"
        - merchantId [string, default null]: merchant ID. ex: "5656565656565656"
        - merchantName [string, default null]: merchant name. ex: "Google Cloud Platform"
        - walletId [string, default null]: virtual wallet ID. ex: "5656565656565656"
        - methodCode [string, default null]: method code. ex: "chip", "token", "server", "manual", "magstripe" or "contactless"
        - score [float, default 0.0]: internal score calculated for the authenticity of the purchase. ex: 7.6
        - issuingTransactionIds [string, default null]: ledger transaction ids linked to this Purchase
        - endToEndId [string, default null]: central bank's unique transaction ID. ex: "E79457883202101262140HHX553UPqeq"
        - status [string, default null]: current IssuingCard status. ex: "approved", "canceled", "denied", "confirmed" or "voided"
        - tags [string, default null]: list of strings for tagging. ex: ["travel", "food"]
        - created [string, default null]: creation datetime for the IssuingPurchase. ex: "2020-03-10 10:30:00.000"
        - updated [string, default null]: latest update datetime for the IssuingPurchase. ex: "2020-03-10 10:30:00.000"
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->holderName = Checks::checkParam($params, "holderName");
        $this->cardId = Checks::checkParam($params, "cardId");
        $this->cardEnding = Checks::checkParam($params, "cardEnding");
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
        $this->walletId = Checks::checkParam($params, "walletId");
        $this->methodCode = Checks::checkParam($params, "methodCode");
        $this->score = Checks::checkParam($params, "score");
        $this->issuingTransactionIds = Checks::checkParam($params, "issuingTransactionIds");
        $this->endToEndId = Checks::checkParam($params, "endToEndId");
        $this->status = Checks::checkParam($params, "status");
        $this->tags = Checks::checkParam($params, "tags");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

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

    Receive a generator of IssuingPurchase objects previously created in the Stark Infra API

    ## Parameters (optional):
        - endToEndIds [list of strings, default []]: central bank's unique transaction ID. ex: "E79457883202101262140HHX553UPqeq"
        - holderIds [list of strings, default []]: card holder IDs. ex: ["5656565656565656", "4545454545454545"]
        - cardIds [list of strings, default []]: card  IDs. ex: ["5656565656565656", "4545454545454545"]
        - status [string, default null]: filter for status of retrieved objects. ex: "approved", "canceled", "denied", "confirmed" or "voided"
        - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - ids [list of strings, default [], default null]: purchase IDs
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - tags [list of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - generator of IssuingPurchase objects with updated attributes
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
        - endToEndIds [list of strings, default []]: central bank's unique transaction ID. ex: "E79457883202101262140HHX553UPqeq"
        - holderIds [list of strings, default []]: card holder IDs. ex: ["5656565656565656", "4545454545454545"]
        - cardIds [list of strings, default []]: card  IDs. ex: ["5656565656565656", "4545454545454545"]
        - status [string, default null]: filter for status of retrieved objects. ex: "approved", "canceled", "denied", "confirmed" or "voided"
        - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - ids [list of strings, default [], default null]: purchase IDs
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - tags [list of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - user [Organization/Project object, default null, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - list of IssuingPurchase objects with updated attributes
        - cursor to retrieve the next page of IssuingPurchase objects
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, IssuingPurchase::resource(), $options);
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
