<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class IssuingTokenRequest extends SubResource
{

    public $cardId;
    public $walletId;
    public $methodCode;
    public $content;
    public $signature;
    public $metadata;

    /**
    # issuingTokenRequest object

    The IssuingTokenRequest object displays the necessary information to proceed with the card tokenization.

    ## Attributes (required):
        - cardId [string]: card ID which the token is bounded to. ex: "5656565656565656"
        - wallet_id [string]: desired wallet to be integrated. ex: "google"
        - method_code [string]: method code. ex: "app" or "manual"
        
    ## Attributes (return-only):
        - content [string]: token request content. ex: "eyJwdWJsaWNLZXlGaW5nZXJwcmludCI6ICJlNTNiZThjZTRhYWQxNWU2OWNmMjExOTA5Mjk4YzJkOTE0O..."
        - signature [string]: token request signature. ex: "eyJwdWJsaWNLZXlGaW5nZXJwcmludCI6ICJlNTNiZThjZTRhYWQxNWU2OWNmMjExOTA5Mjk4YzJkOTE0O..."
        - metadata [dictionary object]: dictionary object used to store additional information about the IssuingPurchase object. ex: [authorizationId => "OjZAqj"]
    */
    function __construct(array $params)
    {
        $this-> cardId = Checks::checkParam($params, "cardId");
        $this-> walletId = Checks::checkParam($params, "walletId");
        $this-> methodCode = Checks::checkParam($params, "methodCode");
        $this-> content = Checks::checkParam($params, "content");
        $this-> signature = Checks::checkParam($params, "signature");
        $this-> metadata = Checks::checkParam($params, "metadata");

        Checks::checkParams($params);
    }

    /**
    # Create IssuingTokenRequest

    Send an IssuingTokenRequest object to Stark Infra API to create the payload to proceed with the card tokenization

    ## Parameters (required):
        - request [array of IssuingTokenRequest objects]: array of IssuingTokenRequest objects to be created in the API.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of IssuingTokenRequest objects with updated attributes
     */
    public static function create($entity, $user = null)
    {
        return Rest::postSingle($user, IssuingTokenRequest::resource(), $entity);
    }

    private static function resource()
    {
        $method = function ($array) {
            return new IssuingTokenRequest($array);
        };
        return [
            "name" => "IssuingTokenRequest",
            "maker" => $method,
        ];
    }
}
