<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Parse;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class PixClaim extends Resource
{

    public $accountCreated;
    public $accountNumber;
    public $accountType;
    public $branchCode;
    public $name;
    public $taxId;
    public $keyId;
    public $tags;
    public $status;
    public $type;
    public $keyType;
    public $flow;
    public $claimerBankCode;
    public $claimedBankCode;
    public $created;
    public $updated;

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
        - keyId [string]: id of the registered PixKey to be claimed. Allowed keyTypes are CPF, CNPJ, phone number or email. ex: "+5511989898989".

    ## Parameters (optional):
        - tags [array of strings, default []]: array of strings for tagging. ex: ["travel", "food"]
    
    ## Attributes (return-only):
        - id [string]: unique id returned when the PixClaim is created. ex: "5656565656565656".
        - status [string]: current PixClaim status. Options: "created", "failed", "delivered", "confirmed", "success", "canceled".
        - type [string]: type of Pix Claim. Options: "ownership", "portability".
        - keyType [string]: keyType of the claimed PixKey. Options: "CPF", "CNPJ", "phone" or "email".
        - flow [string]: Options: "claimer" if you requested the PixClaim or "claimed" if you received a PixClaim request.
        - claimerBankCode [string]: bankCode of the Pix participant that created the PixClaim. ex: "20018183".
        - claimedBankCode [string]: bankCode of the account donating the PixKey. ex: "20018183".
        - created [DateTime]: created datetime for the PixClaim.
        - updated [DateTime]: latest update datetime for the PixClaim.
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
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> status = Checks::checkParam($params, "status");
        $this-> type = Checks::checkParam($params, "type");
        $this-> keyType = Checks::checkParam($params, "keyType");
        $this-> flow = Checks::checkParam($params, "flow");
        $this-> claimerBankCode = Checks::checkParam($params, "claimerBankCode");
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

    Receive an enumerator of PixClaim objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "confirmed", "success", "canceled".
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [strings, default null]: filter for the type of retrieved PixClaims. Options: "ownership" or "portability".
        - keyType [string, default null]: filter for the PixKey type of retrieved PixClaims. Options: "cpf", "cnpj", "phone", "email" and "evp",
        - keyId [string, default null]: filter PixClaims linked to a specific PixKey id. Example: "+5511989898989".
        - flow [string, default null]: direction of the Pix Claim. Options: "in" if you received the PixClaim or "out" if you created the PixClaim.
        - tags [array of strings, default null]: array of strings to filter retrieved objects. ex: ["travel", "food"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - enumerator of PixClaim objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getList($user, PixClaim::resource(), $options);
    }

    /**
    # Retrieve paged PixClaims

    Receive a list of up to 100 PixClaim objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.
    
    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "confirmed", "success", "canceled"
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [strings, default null]: filter for the type of retrieved PixClaims. Options: "ownership" or "portability".
        - keyType [string, default null]: filter for the PixKey type of retrieved PixClaims. Options: "cpf", "cnpj", "phone", "email" and "evp",
        - keyId [string, default null]: filter PixClaims linked to a specific PixKey id. Example: "+5511989898989".
        - flow [string, default null]: direction of the Pix Claim. Options: "in" if you received the PixClaim or "out" if you created the PixClaim.
        - tags [array of strings, default null]: array of strings to filter retrieved objects. ex: ["travel", "food"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - list of PixClaim objects with updated attributes
        - cursor to retrieve the next page of PixClaim objects
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
        - params [dictionary of optional parameters]:
            - reason [string, default: "userRequested"]: reason why the PixClaim is being patched. Options: "fraud", "userRequested".
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixClaim with updated attributes
     */
    public static function update($id, $status, $params = [], $user = null)
    {
        $params["status"] = $status;
        return Rest::patchId($user, PixClaim::resource(), $id, $params);
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
