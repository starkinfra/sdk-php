<?php

namespace StarkInfra;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\StarkDate;
use StarkInfra\Utils\Parse;


class PixClaim extends Resource
{
    /**
    # PixClaim object

    PixClaims intend to transfer a PixKey from one account to another.
    When you initialize a PixClaim, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - accountCreated [Date, Datetime or string]: opening Date or DateTime for the account claiming the PixKey. ex: "2020-03-10 10:30:00.000"
        - accountNumber [string]: number of the account claiming the PixKey. ex: "76543".
        - accountType [string]: type of the account claiming the PixKey. Options: "checking", "savings", "salary" or "payment".
        - branchCode [string]: branch code of the account claiming the PixKey. ex: 1234".
        - name [string]: holder's name of the account claiming the PixKey. ex: "Jamie Lannister".
        - taxId [string]: holder's taxId of the account claiming the PixKey (CPF/CNPJ). ex: "012.345.678-90".
        - keyId [string]: id of the registered Pix Key to be claimed. Allowed keyTypes are CPF, CNPJ, phone number or email. ex: "+5511989898989".
    
    ## Attributes (return-only):
        - id [string]: unique id returned when the PixClaim is created. ex: "5656565656565656".
        - status [string]: current PixClaim status. Options: "created", "failed", "delivered", "confirmed", "success", "canceled".
        - type [string]: type of Pix Claim. Options: "ownership", "portability".
        - keyType [string]: keyType of the claimed PixKey. Options: "CPF", "CNPJ", "phone" or "email".
        - agent [string]: Options: "claimer" if you requested the PixClaim or "claimed" if you received a PixClaim request.
        - bankCode [string]: bankCode of the account linked to the PixKey being claimed. ex: "20018183".
        - claimedBankCode [string]: bankCode of the account donating the PixKey. ex: "20018183".
        - created [DateTime]: created datetime for the PixClaim.
        - updated [DateTime]: update datetime for the PixClaim.
    */

    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> accountCreated = Checks::checkDateTime(Checks::checkParam($params, "accountCreated"));
        $this-> accountNumber = Checks::checkParam($params, "accountNumber");
        $this-> accountType = Checks::checkParam($params, "accountType");
        $this-> branchCode = Checks::checkParam($params, "branchCode");
        $this-> name = Checks::checkParam($params, "name");
        $this-> taxId = Checks::checkParam($params, "taxId");
        $this-> keyId = Checks::checkParam($params, "keyId");
        $this-> status = Checks::checkParam($params, "status");
        $this-> type = Checks::checkParam($params, "type");
        $this-> keyType = Checks::checkParam($params, "keyType");
        $this-> agent = Checks::checkParam($params, "agent");
        $this-> bankCode = Checks::checkParam($params, "bankCode");
        $this-> claimedBankCode = Checks::checkParam($params, "claimedBankCode");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create PixClaim objects
    
    Create a PixClaim to request the transfer of a PixKey to an account
    hosted at other Pix participants in the Stark Infra API.
    
    ## Parameters (required):
        - claim [PixClaim object]: PixClaim object to be created in the API.
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixClaim object with updated attributes.
     */
    public static function create($claims, $user = null)
    {
        return Rest::postSingle($user, PixClaim::resource(), $claims);
    }

    /**
    # Retrieve a PixClaim object

    Retrieve a PixClaim object linked to your Workspace in the Stark Infra API by its id.
    
    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixClaim object that corresponds to the given id.
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, PixClaim::resource(), $id);
    }

    /**
    # Retrieve PixClaim objects

    Receive a generator of PixClaims objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [list of strings, default null]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "confirmed", "success", "canceled".
        - ids [list of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [strings, default null]: filter for the type of retrieved PixClaims. Options: "ownership" or "portability".
        - agent [string, default null]: filter for the agent of retrieved PixClaims. Options: "claimer" or "claimed".
        - keyType [string, default null]: filter for the PixKey type of retrieved PixClaims. Options: "cpf", "cnpj", "phone", "email" and "evp",
        - keyId [string, default null]: filter PixClaims linked to a specific PixKey id. Example: "+5511989898989".
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - generator of PixClaim objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        $options["status"] = Checks::checkParam($options, "status");
        $options["ids"] = Checks::checkParam($options, "ids");
        $options["type"] = Checks::checkParam($options, "type");
        $options["agent"] = Checks::checkParam($options, "agent");
        $options["keyType"] = Checks::checkParam($options, "keyType");
        $options["keyId"] = Checks::checkParam($options, "keyId");

        return Rest::getList($user, PixClaim::resource(), $options);
    }

    /**
    # Retrieve paged PixClaims

    Receive a list of up to 100 PixClaims objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.
    
    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [list of strings, default null]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "confirmed", "success", "canceled"
        - ids [list of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [strings, default null]: filter for the type of retrieved PixClaims. Options: "ownership" or "portability".
        - agent [string, default null]: filter for the agent of retrieved PixClaims. Options: "claimer" or "claimed".
        - keyType [string, default null]: filter for the PixKey type of retrieved PixClaims. Options: "cpf", "cnpj", "phone", "email" and "evp",
        - keyId [string, default null]: filter PixClaims linked to a specific PixKey id. Example: "+5511989898989".
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - list of PixClaim objects with updated attributes and cursor to retrieve the next page of PixClaim objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, PixClaim::resource(), $options);
    }

    /**
    # Update PixClaim entity

    Update a PixClaim parameters by passing id.

    ## Parameters (required):
        - id [string]: PixClaim id. ex: '5656565656565656'
        - status [string]: patched status for Pix Claim. Options: "confirmed" and "canceled"
    
    ## Parameters (optional):
        - reason [string, default: "userRequested"]: reason why the PixClaim is being patched. Options: "fraud", "userRequested".
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixClaim with updated attributes
     */
    public static function update($id, $options = [], $user = null)
    {
        $options["status"] = Checks::checkParam($options, "status");
        return Rest::patchId($user, PixClaim::resource(), $id, $options);
    }

    /**
    # Cancel a PixClaim entity
    
    Cancel a PixClaim entity previously created in the Stark Infra API
    
    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - canceled PixClaim object
     */
    public static function cancel($id, $user = null)
    {
        return Rest::deleteId($user, PixClaim::resource(), $id);
    }

    private static function resource()
    {
        $claim = function ($array) {
            return new PixClaim($array);
        };
        return [
            "name" => "PixClaim",
            "maker" => $claim,
        ];
    }
}
