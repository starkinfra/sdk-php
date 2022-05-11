<?php

namespace StarkInfra;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\StarkDate;
use StarkInfra\Utils\Parse;


class PixClaim extends Resource
{
    /*
    # PixClaim object

    PixClaims intend to transfer a PixKey from one account to another.
    When you initialize a PixClaim, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - accountCreated [datetime.date, datetime.datetime or string]: opening Date or DateTime for the account claiming the PixKey. ex: "2022-01-01".
        - accountNumber [string]: number of the account claiming the PixKey. ex: "76543".
        - accountType [string]: type of the account claiming the PixKey. Options: "checking", "savings", "salary" or "payment".
        - branchCode [string]: branch code of the account claiming the PixKey. ex: 1234".
        - name [string]: holder's name of the account claiming the PixKey. ex: "Jamie Lannister".
        - taxId [string]: holder's taxId of the account claiming the PixKey (CPF/CNPJ). ex: "012.345.678-90".
        - keyId [string]: id of the registered Pix Key to be claimed. Allowed keyTypes are CPF, CNPJ, phone number or email. ex: "+5511989898989".
    
    ## Attributes (return-only):
        - id [string, default None]: unique id returned when the PixClaim is created. ex: "5656565656565656".
        - status [string, default None]: current PixClaim status. Options: "created", "failed", "delivered", "confirmed", "success", "canceled".
        - type [string]: type of Pix Claim. Options: "ownership", "portability".
        - keyType [string, default None]: keyType of the claimed PixKey. Options: "CPF", "CNPJ", "phone" or "email".
        - agent [string, default None]: Options: "claimer" if you requested the PixClaim or "claimed" if you received a PixClaim request.
        - bankCode [string, default None]: bank_code of the account linked to the PixKey being claimed. ex: "20018183".
        - claimedBankCode [string, default None]: bank_code of the account donating the PixKey. ex: "20018183".
        - created [datetime.datetime, default None]: creation datetime for the PixClaim. ex: datetime.datetime(2020, 3, 10, 10, 30, 0, 0)
        - updated [datetime.datetime, default None]: update datetime for the PixClaim. ex: datetime.datetime(2020, 3, 10, 10, 30, 0, 0)
    */

    function __construct(array $params)
    {
        parent::__construct($params);

        $this->accountCreated = Checks::checkDateTime(Checks::checkParam($params, "accountCreated"));
        $this->accountNumber = Checks::checkParam($params, "accountNumber");
        $this->accountType = Checks::checkParam($params, "accountType");
        $this->branchCode = Checks::checkParam($params, "branchCode");
        $this->name = Checks::checkParam($params, "name");
        $this->taxId = Checks::checkParam($params, "taxId");
        $this->keyId = Checks::checkParam($params, "keyId");
        $this->status = Checks::checkParam($params, "status");
        $this->type = Checks::checkParam($params, "type");
        $this->keyType = Checks::checkParam($params, "keyType");
        $this->agent = Checks::checkParam($params, "agent");
        $this->bankCode = Checks::checkParam($params, "bankCode");
        $this->claimedBankCode = Checks::checkParam($params, "claimedBankCode");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /*
    # Create a PixClaim object
    
    Create a PixClaim to request the transfer of a PixKey to an account
    hosted at other Pix participants in the Stark Infra API.
    
    ## Parameters (required):
        - claim [PixClaim object]: PixClaim object to be created in the API.
    
    ## Parameters (optional):
        - user [Organization/Project object, default None]: Organization or Project object. Not necessary if starkinfra.user was set before function call
    
    ## Return:
        - PixClaim object with updated attributes.
     */

    public static function create($pixClaims, $user = null)
    {
        return Rest::postSingle($user, PixClaim::resource(), $pixClaims);
    }

    /*
    # Retrieve a PixClaim object

    Retrieve a PixClaim object linked to your Workspace in the Stark Infra API by its id.
    
    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default None]: Organization or Project object. Not necessary if starkinfra.user was set before function call
    
    ## Return:
        - PixClaim object that corresponds to the given id.
    
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, PixClaim::resource(), $id);
    }

    /*
    # Retrieve PixClaims

    Receive a generator of PixClaims objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [datetime.date or string, default None]: date filter for objects created after a specified date. ex: datetime.date(2020, 3, 10)
        - before [datetime.date or string, default None]: date filter for objects created before a specified date. ex: datetime.date(2020, 3, 10)
        - status [list of strings, default None]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "confirmed", "success", "canceled".
        - ids [list of strings, default None]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [strings, default None]: filter for the type of retrieved PixClaims. Options: "ownership" or "portability".
        - agent [string, default None]: filter for the agent of retrieved PixClaims. Options: "claimer" or "claimed".
        - keyType [string, default None]: filter for the PixKey type of retrieved PixClaims. Options: "cpf", "cnpj", "phone", "email" and "evp",
        - keyId [string, default None]: filter PixClaims linked to a specific PixKey id. Example: "+5511989898989".
        - user [Organization/Project object, default None]: Organization or Project object. Not necessary if starkinfra.user was set before function call
    
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

    /*
    # Retrieve paged PixClaims

    Receive a list of up to 100 PixClaims objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.
    
    ## Parameters (optional):
        - cursor [string, default None]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [datetime.date or string, default None]: date filter for objects created after a specified date. ex: datetime.date(2020, 3, 10)
        - before [datetime.date or string, default None]: date filter for objects created before a specified date. ex: datetime.date(2020, 3, 10)
        - status [list of strings, default None]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "confirmed", "success", "canceled"
        - ids [list of strings, default None]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [strings, default None]: filter for the type of retrieved PixClaims. Options: "ownership" or "portability".
        - agent [string, default None]: filter for the agent of retrieved PixClaims. Options: "claimer" or "claimed".
        - keyType [string, default None]: filter for the PixKey type of retrieved PixClaims. Options: "cpf", "cnpj", "phone", "email" and "evp",
        - keyId [string, default None]: filter PixClaims linked to a specific PixKey id. Example: "+5511989898989".
        - user [Organization/Project object, default None]: Organization or Project object. Not necessary if starkinfra.user was set before function call
    
    ## Return:
        - list of PixClaim objects with updated attributes and cursor to retrieve the next page of PixClaim objects
    
     */

    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, PixClaim::resource(), $options);
    }

    /*
    # Update PixClaim entity

    Update a PixClaim parameters by passing id.

    ## Parameters (required):
        - id [string]: PixClaim id. ex: '5656565656565656'
        - status [string]: patched status for Pix Claim. Options: "confirmed" and "canceled"
    
    ## Parameters (optional):
        - reason [string, default: "userRequested"]: reason why the PixClaim is being patched. Options: "fraud", "userRequested".
        - user [Organization/Project object, default None]: Organization or Project object. Not necessary if starkinfra.user was set before function call
    
    ## Return:
        - PixClaim with updated attributes
    
     */

    public static function update($id, $options = [], $user = null)
    {
        $options["status"] = Checks::checkParam($options, "status");
        return Rest::patchId($user, PixClaim::resource(), $id, $options);
    }

    public static function parse($content, $signature, $user = null)
    {
        return Parse::parseAndVerify($content, $signature, PixClaim::resource(), $user);
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
