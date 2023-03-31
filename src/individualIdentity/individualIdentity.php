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

    An IndividualDocument represents an individual to be validated. It can have several individual documents attached
    to it, which are used to validate the identity of the individual. Once an individual identity is created, individual
    documents must be attached to it using the created method of the individual document resource. When all the required
    individual documents are attached to an individual identity it can be sent to validation by patching its status to 
    processing.

    When you initialize an IndividualIdentity, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the array of created objects.

    ## Parameters (required):
        - name [integer]: individual's full name. ex: "Edward Stark"
        - taxId [string]: individual's tax ID (CPF). ex: "594.739.480-42"

    ## Parameters (optional):
        - tags [array of strings, default null]: array of strings for reference when searching for IndividualIdentities. ex: ["employees", "monthly"]

    ## Attributes (return-only):
        - id [string]: Unique id returned when the identity is created. ex: "5656565656565656"
        - status [string]: current status of the IndividualIdentity. Options: "created", "canceled", "processing", "failed", "success"
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
        - status [string]: You may send IndividualDocuments to validation by passing 'processing' in the status

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

    Cancel an IndividualIdentity entity previously created in the Stark Infra API

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
