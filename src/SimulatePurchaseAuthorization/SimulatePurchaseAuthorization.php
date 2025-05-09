<?php

namespace StarkInfra;
use StarkCore\Utils\API;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;

class SimulatePurchaseAuthorization extends Resource
{
    public $id;
    public $cardNumber;
    public $cardExpiration;
    public $securityCode;
    public $amount;
    public $merchantName;
    public $merchantCategoryCode;
    public $merchantCountryCode;
    public $merchantCurrencyCode;
    public $methodCode;
    public $walletId;
    public $status;
    public $partial;

    /**
    # SimulatePurchaseAuthorization object

    Displays the SimulatePurchaseAuthorization objects created to your Workspace.

    ## Attributes (request):
        - cardNumber [string]: card number used for the purchase. ex: "1122334455667788"
        - cardExpiration [string]: card expiration date in the format YYYY-MM. ex: "2025-07"
        - securityCode [string]: card security code. ex: "123"
        - amount [integer]: purchase amount in cents. ex: 2500 (= R$ 25.00)
        - merchantName [string]: merchant name. ex: "Test Merchant 1"
        - merchantCategoryCode [string]: merchant category code. ex: "hotelsMotelsAndResorts"
        - merchantCountryCode [string]: merchant country code. ex: "BRA"
        - merchantCurrencyCode [string]: currency code. ex: "BRL"
        - methodCode [string]: method code. Options: "chip", "token", "server", "manual" or "contactless"
        - walletId [string]: wallet ID. ex: "apple"
        - status [string, default null]: status of the purchase. Options: "approved", "canceled", "denied", "confirmed", "voided"
        - partial [boolean, default false]: if the purchase is partial.

    ## Attributes (return-only):
        - id [string]: unique id returned when SimulatePurchaseAuthorization is created. ex: "5656565656565656"
        - status [string]: current status. Options: "approved", "canceled", "denied", "confirmed", "voided"
        - created [DateTime]: creation datetime for the SimulatePurchaseAuthorization.
        - updated [DateTime]: latest update datetime for the SimulatePurchaseAuthorization.
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->cardNumber = Checks::checkParam($params, "cardNumber");
        $this->cardExpiration = Checks::checkParam($params, "cardExpiration");
        $this->securityCode = Checks::checkParam($params, "securityCode");
        $this->amount = Checks::checkParam($params, "amount");
        $this->merchantName = Checks::checkParam($params, "merchantName");
        $this->merchantCategoryCode = Checks::checkParam($params, "merchantCategoryCode");
        $this->merchantCountryCode = Checks::checkParam($params, "merchantCountryCode");
        $this->merchantCurrencyCode = Checks::checkParam($params, "merchantCurrencyCode");
        $this->methodCode = Checks::checkParam($params, "methodCode");
        $this->walletId = Checks::checkParam($params, "walletId");
        $this->status = Checks::checkParam($params, "status");
        $this->partial = Checks::checkParam($params, "partial");
        
        Checks::checkParams($params);
    }
    
    /**
    # Simulate a SimulatePurchaseAuthorization
    
    Send a SimulatePurchaseAuthorization request to simulate a purchase authorization
    in the sandbox environment.
    
    ## Parameters (required):
        - params [array]: array with purchase authorization parameters
            - id [string]: unique id returned when SimulatePurchaseAuthorization is created. ex: "5656565656565656"
            - cardNumber [string]: card number used for the purchase. ex: "1122334455667788"
            - cardExpiration [string]: card expiration date in the format YYYY-MM. ex: "2025-07"
            - securityCode [string]: card security code. ex: "123"
            - amount [integer]: purchase amount in cents. ex: 2500 (= R$ 25.00)
            - merchantName [string]: merchant name. ex: "Test Merchant 1"
            - merchantCategoryCode [string]: merchant category code. ex: "hotelsMotelsAndResorts"
            - merchantCountryCode [string]: merchant country code. ex: "BRA"
            - merchantCurrencyCode [string]: currency code. ex: "BRL"
            - methodCode [string]: method code. Options: "chip", "token", "server", "manual", "contactless"
            - walletId [string, default null]: wallet ID. ex: "apple"
            - status [string, default null]: status of the purchase. Options: "approved", "canceled", "denied", "confirmed", "voided"
            - partial [boolean, default false]: if the purchase is partial.
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - SimulatePurchaseAuthorization object with simulated purchase data
    */
    public static function purchase($params, $user = null)
    {
        return Rest::postRaw($user, 'ditto/issuing-purchase', $params,'joker');
    }
} 