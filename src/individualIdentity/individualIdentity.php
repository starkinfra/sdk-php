<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class IndividualIdentity extends Resource
{

    public $name;
    public $taxId;
    public $tags;
    public $status;
    public $created;

    /**
    # IndividualIdentity object

    An IndividualIdentity represents an end-to-end identity verification of a Brazilian individual, created with name, email, deliveryMethod ("automatic" or "manual") and proofs ("identity" and/or "biometric") as required parameters, and taxId, phone and tags as optional; the created object already carries a validatorLink the holder uses to submit each proof directly.

    When you initialize an IndividualIdentity, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the array of created objects.

    ## Parameters (required):
        - name [string]: individual's full name. ex: "Edward Stark"
        - email [string]: e-mail used to deliver the validatorLink when deliveryMethod is "automatic".
        - deliveryMethod [string]: how the validatorLink reaches the holder. Options: "automatic", "manual".
        - proofs [array of strings]: proof types to collect. Options: "identity", "biometric"; each must be enabled on your workspace's Identity Profile.

    ## Parameters (optional):
        - taxId [string, default null]: individual's tax ID (CPF); can be set later via update(). ex: "594.739.480-42"
        - phone [string, default null]: holder's phone in international format.
        - tags [array of strings, default null]: array of strings for reference when searching for IndividualIdentities. ex: ["employees", "monthly"]

    ## Attributes (return-only):
        - id [string]: Unique id returned when the identity is created. ex: "5656565656565656"
        - status [string]: current status of the IndividualIdentity. Options: "created", "processing", "pending", "success", "failed" — "canceled" is not a valid value; a canceled identity shows status "failed".
        - created [DateTime]: creation datetime for the IndividualIdentity.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> name = Checks::checkParam($params, "name");
        $this-> taxId = Checks::checkParam($params, "taxId");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> status = Checks::checkParam($params, "status");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));

        Checks::checkParams($params);
    }

    /**
    # Create IndividualIdentities

    Send an array of IndividualIdentity objects for creation in the Stark Infra API

    ## Parameters (required):
        - identities [array of IndividualIdentity objects]: array of IndividualIdentity objects to be created in the API.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of IndividualIdentity objects with updated attributes
     */
    public static function create($identities, $user = null)
    {
        return Rest::post($user, IndividualIdentity::resource(), $identities);
    }

    /**
    # Retrieve a specific IndividualIdentity

    Receive a single IndividualIdentity object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IndividualIdentity object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IndividualIdentity::resource(), $id);
    }

    /**
    # Retrieve IndividualIdentities

    Receive an enumerator of IndividualIdentity objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. Options: "created", "canceled", "processing", "failed" and "success"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IndividualIdentity objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, IndividualIdentity::resource(), $options);
    }

    /**
    # Retrieve paged IndividualIdentities

    Receive a list of up to 100 IndividualIdentity objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. Options: "created", "canceled", "processing", "failed" and "success"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of IndividualIdentity objects with updated attributes
        - cursor to retrieve the next page of IndividualIdentity objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, IndividualIdentity::resource(), $options);
    }

    /**
    # Update IndividualIdentity entity

    Update an IndividualIdentity by passing id.

    ## Parameters (required):
        - id [string]: IndividualIdentity id. ex: "5656565656565656"
        - status [string]: pass 'processing' to trigger validation once the holder has submitted the required proofs through the validatorLink

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - target IndividualIdentity with updated attributes
     */
    public static function update($id, $status, $user = null)
    {
        $params["status"] = $status;
        return Rest::patchId($user, IndividualIdentity::resource(), $id, $params);
    }

    /**
    # Cancel an IndividualIdentity entity

    Cancel an IndividualIdentity entity previously created in the Stark Infra API. This is a soft delete: the identity moves to "failed", every pending proof is canceled, and a "canceled" log is delivered through the webhook. Only identities in "created" or "pending" status can be canceled.

    ## Parameters (required):
        - id [string]: IndividualIdentity unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - canceled IndividualIdentity object
     */
    public static function cancel($id, $user = null)
    {
        return Rest::deleteId($user, IndividualIdentity::resource(), $id);
    }

    private static function resource()
    {
        $identity = function ($array) {
            return new IndividualIdentity($array);
        };
        return [
            "name" => "IndividualIdentity",
            "maker" => $identity,
        ];
    }
}
