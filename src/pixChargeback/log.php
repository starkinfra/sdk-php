<?php

namespace StarkInfra\PixChargeback;
use StarkCore\Utils\API;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;
use StarkInfra\PixChargeback;


class Log extends Resource
{

    public $created;
    public $type;
    public $errors;
    public $chargeback;

    /**
    # PixChargeback\Log object
    
    Every time a PixChargeback entity is modified, a corresponding PixChargeback\Log
    is generated for the entity. This log is never generated by the user.

    ## Attributes (return-only):
        - id [string]: unique id returned when the log is created. ex: "5656565656565656"
        - created [string]: creation datetime for the log. 
        - type [string]: type of the PixChargeback event which triggered the log creation. 
        - errors [array of strings]: list of errors linked to this PixChargeback event
        - chargeback [PixChargeback]: PixChargeback entity to which the log refers to.
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> type = Checks::checkParam($params, "type");
        $this-> errors = Checks::checkParam($params, "errors");
        $this-> chargeback = Checks::checkParam($params, "chargeback");

        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific PixChargeback\Log
    
    Receive a single PixChargeback\Log object previously created by the Stark Infra API by its id
    
    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixChargeback\Log object with updated attributes
     */
    public static function get($id, $user=null)
    {
        return Rest::getId($user, Log::resource(), $id);
    }

    /**
    # Retrieve PixChargeback\Logs
    
    Receive an enumerator of PixChargeback\Log objects previously created in the Stark Infra API
    
    ## Parameters (optional):
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [array of strings, default null]: filter retrieved objects by types. ex: "created", "failed", "delivering", "delivered", "closed", "canceled".
        - chargebackIds [array of strings, default null]: list of PixChargeback IDs to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - ids [array of strings, default null]: Log ids to filter PixChargeback Logs. ex: ["5656565656565656"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - enumerator of PixChargeback\Log objects with updated attributes
    */
    public static function query($options=[], $user=null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
    
        return Rest::getList($user, Log::resource(), $options);
    }

    /**
    # Retrieve paged PixChargeback\Logs
    
    Receive a list of up to 100 PixChargeback\Log objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.
    
    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [array of strings, default null]: filter retrieved objects by types. ex: "created", "failed", "delivering", "delivered", "closed", "canceled".
        - chargebackIds [array of strings, default null]: list of PixChargeback IDs to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - ids [array of strings, default null]: Log ids to filter PixChargeback Logs. ex: ["5656565656565656"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - list of PixChargeback\Log objects with updated attributes
        - cursor to retrieve the next page of PixChargeback\Log objects
    */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        
        return Rest::getPage($user, Log::resource(), $options);
    }

    private static function resource()
    {
        $chargebackLog = function ($array) {
            $chargeback = function ($array) {
                return new PixChargeback($array);
            };
            $array["chargeback"] = API::fromApiJson($chargeback, $array["chargeback"]);
            return new Log($array);
        };
        return [
            "name" => "PixChargebackLog",
            "maker" => $chargebackLog,
        ];
    }
}
