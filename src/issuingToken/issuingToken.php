<?php

namespace StarkInfra;
use StarkCore\Utils\API;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Parse;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class IssuingToken extends Resource
{

    public $cardId;
    public $walletId;
    public $walletName;
    public $merchantId;
    public $externalId;
    public $tags;
    public $status;
    public $created;
    public $updated;
    public $activationCode;
    public $methodCode;
    public $deviceType;
    public $deviceName;
    public $deviceSerialNumber;
    public $deviceOsName;
    public $deviceOsVersion;
    public $deviceImei;
    public $walletInstanceId;

    /**
    # IssuingToken object

    The IssuingToken object displays the information of the tokens created in your Workspace.

    ## Attributes (return-only):
        - cardId [string]: card ID which the token is bounded to. ex: "5656565656565656"
        - walletId [string]: wallet provider which the token is bounded to. ex: "google"
        - walletName [string]: wallet name. ex: "GOOGLE"
        - merchantId [string]: merchant unique id. ex: "5656565656565656"

    ## Attributes (IssuingToken only):
        - id [string]: unique id returned when IssuingToken is created. ex: "5656565656565656"
        - externalId [string]: a unique string among all your IssuingTokens, used to avoid resource duplication. ex: "DSHRMC00002626944b0e3b539d4d459281bdba90c2588791"
        - tags [list of strings]: list of strings for reference when searching for IssuingToken. ex: ["employees", "monthly"]
        - status [string]: current IssuingToken status. ex: "active", "blocked", "canceled", "frozen" or "pending"
        - created [DateTime]: creation datetime for the IssuingToken.
        - updated [DateTime]: latest update datetime for the IssuingToken.
        
    ## Attributes (authorization request only):
        - activationCode [string]: activation code recived through the bank app or sms. ex: "481632" 
        - methodCode [string]: provisioning method. Options: "app", "token", "manual", "server" or "browser"
        - deviceType [string]: device type used for tokenization. ex: "Phone"
        - deviceName [string]: device name used for tokenization. ex: "My phone" 
        - deviceSerialNumber [string]: device serial number used for tokenization. ex: "2F6D63"
        - deviceOsName [string]: device operational system name used for tokenization. ex: "Android"
        - deviceOsVersion [string]: device operational system version used for tokenization. ex: "4.4.4"
        - deviceImei [string]: device imei used for tokenization. ex: "352099001761481"
        - walletInstanceId [string]: unique id refered to the wallet app in the current device. ex: "71583be4777eb89aaf0345eebeb82594f096615ed17862d0"
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->cardId = Checks::checkParam($params, "cardId");
        $this->walletId = Checks::checkParam($params, "walletId");
        $this->walletName = Checks::checkParam($params, "walletName");
        $this->merchantId = Checks::checkParam($params, "merchantId");
        $this->externalId = Checks::checkParam($params, "externalId");
        $this->tags = Checks::checkParam($params, "tags");
        $this->status = Checks::checkParam($params, "status");
        $this->created = Checks::checkParam($params, "created");
        $this->updated = Checks::checkParam($params, "updated");
        $this->activationCode = Checks::checkParam($params, "activationCode");
        $this->methodCode = Checks::checkParam($params, "methodCode");
        $this->deviceType = Checks::checkParam($params, "deviceType");
        $this->deviceName = Checks::checkParam($params, "deviceName");
        $this->deviceSerialNumber = Checks::checkParam($params, "deviceSerialNumber");
        $this->deviceOsName = Checks::checkParam($params, "deviceOsName");
        $this->deviceOsVersion = Checks::checkParam($params, "deviceOsVersion");
        $this->deviceImei = Checks::checkParam($params, "deviceImei");
        $this->walletInstanceId = Checks::checkParam($params, "walletInstanceId");
        
        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific IssuingToken

    Receive a single IssuingToken object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingToken object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IssuingToken::resource(), $id);
    }

    /**
    # Retrieve IssuingTokens

    Receive an enumerator of IssuingToken objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "active", "blocked", "canceled", "frozen" or "pending"
        - cardIds [list of strings, default null]: list of cardIds to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - tags [list of strings, default null]: list of strings for tagging. ex: ["travel", "food"]
        - ids [array of strings, default null]: issuingToken IDs
        - externalIds [list of strings, default null]: external IDs. ex: ["DSHRMC00002626944b0e3b539d4d459281bdba90c2588791", "DSHRMC00002626941c531164a0b14c66ad9602ee716f1e85"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingToken objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, IssuingToken::resource(), $options);
    }

    /**
    # Retrieve paged IssuingTokens

    Receive a list of up to 100 IssuingTokens objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "active", "blocked", "canceled", "frozen" or "pending"
        - cardIds [list of strings, default null]: list of cardIds to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - tags [list of strings, default null]: list of strings for tagging. ex: ["travel", "food"]
        - ids [array of strings, default [], default null]: issuingToken IDs
        - externalIds [list of strings, default null]: external IDs. ex: ["DSHRMC00002626944b0e3b539d4d459281bdba90c2588791", "DSHRMC00002626941c531164a0b14c66ad9602ee716f1e85"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of IssuingToken objects with updated attributes
        - cursor to retrieve the next page of IssuingToken objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, IssuingToken::resource(), $options);
    }

    /**
    # Update IssuingToken entity

    Update an IssuingToken by passing id.

    ## Parameters (required):
        - id [string]: IssuingToken id. ex: "5656565656565656"
        
        ## Parameters (optional):
            - params [dictionary of optional parameters]:
                - status [string, default null]: You may block the IssuingToken by passing "blocked" or activate by passing "active" in the status. ex: "active", "blocked"
                - tags [string, default null]: list of strings for tagging. ex: ["travel", "food"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - target IssuingToken with updated attributes
     */
    public static function update($id, $params, $user = null)
    {
        return Rest::patchId($user, IssuingToken::resource(), $id, $params);
    }

    /**
    # Cancel an IssuingToken entity

    Cancel an IssuingToken entity previously created in the Stark Infra API

    ## Parameters (required):
        - id [string]: IssuingToken unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - canceled IssuingToken object
     */
    public static function cancel($id, $user = null)
    {
        return Rest::deleteId($user, IssuingToken::resource(), $id);
    }

    /**
    # Create a single verified IssuingToken request from a content string

    Use this method to parse and verify the authenticity of the request received at the informed endpoint.
    Token requests are posted to your registered endpoint whenever IssuingTokens are received.
    If the provided digital signature does not check out with the StarkInfra public key, a stark.exception.InvalidSignatureException will be raised.

    ## Parameters (required):
        - content [string]: response content from request received at user endpoint (not parsed)
        - signature [string]: base-64 digital signature received at response header "Digital-Signature"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - Parsed IssuingToken object
     */
    public static function parse($content, $signature, $user = null)
    {
        return Parse::parseAndVerify($content, $signature, IssuingToken::resource(), $user);
    }

    /** 
    # Helps you respond IssuingToken requests

    When a new tokenization is triggered by your user, a POST request will be made to your registered URL to get your decision to complete the tokenization.
    The POST request must be answered in the following format, within 2 seconds, and with an HTTP status code 200.

    ## Parameters (required):
        - status [string]: sub-issuer response to the authorization. ex: "approved" or "denied"
    
    ## Parameters (conditionally required):
        - reason [string, default null]: denial reason. Options: "other", "bruteForce", "subIssuerError", "lostCard", "invalidCard", "invalidHolder", "expiredCard", "canceledCard", "blockedCard", "invalidExpiration", "invalidSecurityCode", "missingTokenAuthorizationUrl", "maxCardTriesExceeded", "maxWalletInstanceTriesExceeded"
        - activationMethods [list of dictionaries, default null]: list of dictionaries with "type":string and "value":string pairs
        - designId [string, default null]: design unique id. ex: "5656565656565656"
    ## Parameters (optional):
        - tags [array of strings, default null]: tags to filter retrieved object. ex: ["tony", "stark"]

    ## Return:
        - Dumped JSON string that must be returned to us on the IssuingToken request
    */
    public static function responseAuthorization($params)
    {
        $params = ([    
            "authorization" => [
                "status" => Checks::checkParam($params, "status"),
                "reason" => Checks::checkParam($params, "reason"),
                "activationMethods" => Checks::checkParam($params, "activationMethods"),
                "designId" => Checks::checkParam($params, "designId"),
                "tags" => Checks::checkParam($params, "tags"),
            ]
        ]);
        return json_encode(API::apiJson($params));
    }

    /** 
    # Helps you respond IssuingToken activation requests

    When a new token activation is triggered by your user, a POST request will be made to your registered URL for you to confirm the activation code you informed to them. You may identify this request through the present activation_code in the payload.
    The POST request must be answered in the following format, within 2 seconds, and with an HTTP status code 200.

    ## Parameters (required):
        - status [string]: sub-issuer response to the authorization. ex: "approved" or "denied"
    
    ## Parameters (optional):
        - reason [string, default null]: denial reason. Options: "other", "bruteForce", "subIssuerError", "lostCard", "invalidCard", "invalidHolder", "expiredCard", "canceledCard", "blockedCard", "invalidExpiration", "invalidSecurityCode", "missingTokenAuthorizationUrl", "maxCardTriesExceeded", "maxWalletInstanceTriesExceeded"
        - tags [array of strings, default null]: tags to filter retrieved object. ex: ["tony", "stark"]

    ## Return:
        - Dumped JSON string that must be returned to us on the IssuingToken request
    */
    public static function responseActivation($params)
    {
        $params = ([    
            "authorization" => [
                "status" => Checks::checkParam($params, "status"),
                "reason" => Checks::checkParam($params, "reason"),
                "tags" => Checks::checkParam($params, "tags"),
            ]
        ]);
        return json_encode(API::apiJson($params));
    }

    private static function resource()
    {
        $issuingToken = function ($array) {
            return new IssuingToken($array);
        };
        return [
            "name" => "IssuingToken",
            "maker" => $issuingToken,
        ];
    }
}
