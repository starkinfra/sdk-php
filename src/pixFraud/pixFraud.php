<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class PixFraud extends Resource
{
    
    public $bacenId;
    public $keyId;
    public $externalId;
    public $tags;
    public $taxId;
    public $type;
    public $status;
    public $created;
    public $updated;

    /**
    # PixFraud object

    Pix Frauds are used to report the PixKey or taxId when a fraud
    has been confirmed.
    When you initialize a PixFraud, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the created object.
    
    ## Parameters (required):
        - externalId [string]: a string that must be unique among all your PixFrauds, used to avoid resource duplication. ex: "my-internal-id-123456"
        - type [string]: type of PixFraud. Options: "identity", "mule", "scam", "other"
        - taxId [string]: user tax ID (CPF or CNPJ) with or without formatting. ex: "01234567890" or "20.018.183/0001-80"

    ## Parameters (optional):
        - tags [array of strings, default []]: array of strings for tagging. ex: ["fraudulent"]
    
    ## Attributes (return-only):
        - bacenId [string]: id of the bacen institution. ex "5656565656565656"
        - keyId [string]: marked PixKey id. ex: "+5511989898989"
        - id [string]: unique id returned when the PixFraud is created. ex: "5656565656565656"
        - status [string]: current PixFraud status. Options: "created", "failed", "registered", "canceled".
        - created [DateTime]: created datetime for the PixFraud. 
        - updated [DateTime]: latest update datetime for the PixFraud. 
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> externalId = Checks::checkParam($params, "externalId");
        $this-> type = Checks::checkParam($params, "type");
        $this-> taxId = Checks::checkParam($params, "taxId");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> bacenId = Checks::checkParam($params, "bacenId");
        $this-> keyId = Checks::checkParam($params, "keyId");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create PixFraud objects

    Create PixFraud objects in the Stark Infra API
    
    ## Parameters (optional):
        - frauds [array of PixFraud objects]: array of PixFraud objects to be created in the API.
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - array of PixFraud objects with updated attributes
    */
    public static function create($frauds, $user=null)
    {
        return Rest::post($user, PixFraud::resource(), $frauds);
    }

    /** 
    # Retrieve a PixFraud object

    Retrieve the PixFraud object linked to your Workspace in the Stark Infra API using its id.

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656".
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixFraud object that corresponds to the given id.
    */
    public static function get($id, $user=null)
    {
        return Rest::getId($user, PixFraud::resource(), $id);
    }

    /** 
    # Retrieve PixFraud objects
    
    Receive an enumerator of PixFraud objects previously created in the Stark Infra API
    
    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. Options: "created", "failed", "registered", "canceled".
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [array of strings, default null]: filter for the type of retrieved PixFraud. Options: "identity", "mule", "scam", "other"
        - flow [string, default null]: direction of the PixFraud flow. Options: "out" if you created the PixFraud, "in" if you received the PixFraud.
        - tags [array of strings, default null]: array of strings for tagging. ex: ["fraudulent"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - enumerator of PixFraud objects with updated attributes
    */
    public static function query($options=[], $user=null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getList($user, PixFraud::resource(), $options);
    }

    /** 
    # Retrieve paged PixFrauds

    Receive a list of up to 100 PixFraud objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.
    
    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. Options: "created", "failed", "registered", "canceled".
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [array of strings, default null]: filter for the type of retrieved PixFraud. Options: "reversal", "reversalChargeback"
        - flow [string, default null]: direction of the PixFraud flow. Options: "out" if you created the PixFraud, "in" if you received the PixFraud.
        - tags [array of strings, default null]: array of strings for tagging. ex: ["travel", "food"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - list of PixFraud objects with updated attributes
        - cursor to retrieve the next page of PixFraud objects
    */
    public static function page($options = [], $user=null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, PixFraud::resource(), $options);
    }

    /**
    # Cancel a PixFraud entity

    Cancel a PixFraud entity previously created in the Stark Infra API

    ## Parameters (required):
        - id [string]: Pix Fraud unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - canceled PixFraud object
     */
    public static function cancel($id, $user = null)
    {
        return Rest::deleteId($user, PixFraud::resource(), $id);
    }

    private static function resource()
    {
        $fraud = function ($array) {
            return new PixFraud($array);
        };
        return [
            "name" => "PixFraud",
            "maker" => $fraud,
        ];
    }
}
