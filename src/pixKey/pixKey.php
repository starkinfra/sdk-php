<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\StarkDate;


class PixKey extends Resource
{
    /**
    # PixKey object

    PixKeys link bank account information to key ids.
    Key ids are a convenient way to search and pass bank account information.
    When you initialize a PixKey, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - accountCreated [Date, Datetime or string]: opening Date or DateTime for the linked account. ex: "2020-03-10 10:30:00.000"
        - accountNumber [string]: number of the linked account. ex: "76543".
        - accountType [string]: type of the linked account. Options: "checking", "savings", "salary" or "payment".
        - branchCode [string]: branch code of the linked account. ex: "1234".
        - name [string]: holder's name of the linked account. ex: "Jamie Lannister".
        - taxId [string]: holder's taxId (CPF/CNPJ) of the linked account. ex: "012.345.678-90".

    ## Parameters (optional):
        - id [string, default null]: id of the registered PixKey. Allowed types are: CPF, CNPJ, phone number or email. If this parameter is not passed, an EVP will be created. ex: "+5511989898989";
        - tags [array of strings, default null]: list of strings for reference when searching for PixKeys. ex: ["employees", "monthly"]

    ## Attributes (return-only):
        - owned [DateTime]: datetime when the key was owned by the holder. 
        - ownerType [string]: type of the owner of the PixKey. Options: "business" or "individual".
        - status [string]: current PixKey status. Options: "created", "registered", "canceled", "failed"
        - bankCode [string]: bankCode of the account linked to the PixKey. ex: "20018183".
        - bankName [string]: name of the bank that holds the account linked to the PixKey. ex: "StarkBank"
        - type [string]: type of the PixKey. Options: "cpf", "cnpj", "phone", "email" and "evp",
        - created [DateTime]: created datetime for the PixKey. 
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> accountCreated = Checks::checkParam($params, "accountCreated");
        $this-> accountNumber = Checks::checkParam($params, "accountNumber");
        $this-> accountType = Checks::checkParam($params, "accountType");
        $this-> branchCode = Checks::checkParam($params, "branchCode");
        $this-> name = Checks::checkParam($params, "name");
        $this-> taxId = Checks::checkParam($params, "taxId");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> owned = Checks::checkParam($params, "owned");
        $this-> ownerType = Checks::checkParam($params, "ownerType");
        $this-> status = Checks::checkParam($params, "status");
        $this-> bankCode = Checks::checkParam($params, "bankCode");
        $this-> bankName = Checks::checkParam($params, "bankName");
        $this-> type = Checks::checkParam($params, "type");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
    
        Checks::checkParams($params);
    }

    /**
    # Create a PixKey object

    Create a PixKey linked to a specific account in the Stark Infra API
    
    ## Parameters (optional):
        - key [PixKey object]: PixKey object to be created in the API.
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if sStarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixKey object with updated attributes.
    */
    public static function create($keys, $user = null)
    {
        return Rest::postSingle($user, PixKey::resource(), $keys);
    }

    /**
    # Retrieve a PixKey object

    Retrieve the PixKey object linked to your Workspace in the Stark Infra API by its id.
    
    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656".
        - payerId [string]: tax id (CPF/CNPJ) of the individual or business requesting the PixKey information. This id is used by the Central Bank to limit request rates. ex: "20.018.183/0001-80".
    
    ## Parameters (optional):
        - params [dictionary of optional parameters]:
            - endToEndId [string, default null]: central bank's unique transaction id. If the request results in the creation of a PixRequest, the same endToEndId should be used. If this parameter is not passed, one endToEndId will be automatically created. Example: "E00002649202201172211u34srod19le"
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixKey object that corresponds to the given id.
    */
    public static function get($id, $payerId, $params = null, $user = null)
    {
        $params["payerId"] = $payerId;
        return Rest::getId($user, PixKey::resource(), $id, $params);
    }

    /**
    # Retrieve PixKeys

    Receive an enumerator of PixKey objects previously created in the Stark Infra API
    
    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. Options: "created", "registered", "canceled", "failed".
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [array of strings, default null]: filter for the type of retrieved PixKeys. Options: "cpf", "cnpj", "phone", "email", "evp".
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - enumerator of PixKey objects with updated attributes
    */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getList($user, PixKey::resource(), $options);
    }

    /**
    # Retrieve PixKeys

    Receive an enumerator of PixKey objects previously created in the Stark Infra API
    
    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. Options: "created", "registered", "canceled" and "failed"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [array of strings, default null]: filter for the type of retrieved PixKeys. Options: "cpf", "cnpj", "phone", "email", "evp".
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - cursor to retrieve the next page of PixKey objects
        - list of PixKey objects with updated attributes
    */
    public static function page($options = [], $user=null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, PixKey::resource(), $options);
    }

    /**
    # Update PixKey entity

    Update a PixKey parameters by passing id.

    ## Parameters (required):
        - id [string]: PixKey id. ex: '5656565656565656'
        - reason [string]: reason why the PixKey is being patched. Options: "branchTransfer", "reconciliation" or "userRequested".
        - params [dictionary of optional parameters]:
            - accountCreated [Date, DateTime or String]: opening Date or DateTime for the account to be linked. ex: "2020-03-10 10:30:00.000"
            - accountNumber [string, default null]: number of the account to be linked. ex: "76543".
            - accountType [string, default null]: type of the account to be linked. Options: "checking", "savings", "salary" or "payment".
            - branchCode [string, default null]: branch code of the account to be linked. ex: 1234".
            - name [string, default null]: holder's name of the account to be linked. ex: "Jamie Lannister".

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call

    ## Return:
        - PixKey with updated attributes
    */
    public static function update($id, $reason, $params, $user = null)
    {
        $params["reason"] = $reason;
        return Rest::patchId($user, PixKey::resource(), $id, $params);
    }

    /**
    # Cancel a PixKey entity

    Cancel a PixKey entity previously created in the Stark Infra API
    
    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - canceled PixKey object
    */
    public static function cancel($id, $user = null)
    {
        return Rest::deleteId($user, PixKey::resource(), $id);
    }

    private static function resource()
    {
        $key = function ($array) {
            return new PixKey($array);
        };
        return [
            "name" => "PixKey",
            "maker" => $key,
        ];
    }
}
