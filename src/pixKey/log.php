<?php

namespace StarkInfra\PixKey;
use StarkInfra\PixKey;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\API;
use StarkInfra\Utils\StarkDate;
use Test\PixKeyLog\TestPixKeyLog;


class Log extends Resource
{
    /**
    # PixKey\Log object
    
    Every time a PixKey entity is modified, a corresponding PixKey.Log
    is generated for the entity. This log is never generated by the user.
    
    ## Attributes:
        - id [string]: unique id returned when the log is created. ex: "5656565656565656"
        - created [Date, Datetime or string]: creation datetime for the log. ex: "2020-03-10 10:30:00.000"
        - type [string]: type of the PixKey event which triggered the log creation. ex: "created", "registered", "updated", "failed", "canceling" and "canceled".
        - errors [list of strings]: list of errors linked to this PixKey event
        - key [PixKey]: PixKey entity to which the log refers to.
    */    
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> type = Checks::checkParam($params, "type");
        $this-> errors = Checks::checkParam($params, "errors");
        $this-> key = Checks::checkParam($params, "key");

        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific PixKey\Log

    Receive a single PixKey\Log object previously created by the Stark Infra API by its id
    
    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixKey\Log object with updated attributes
     */
     public static function get($id, $user = null)
     {
         return Rest::getId($user, Log::resource(), $id);
     }

     /**
    #Retrieve PixKey\Logs

    Receive an enumerator of PixKey\Log objects previously created in the Stark Infra API
    
    ## Parameters (optional):
        - ids [list of strings, default null]: Log ids to filter PixKey Logs. ex: ["5656565656565656"]
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [list of strings, default null]: filter retrieved objects by types. ex: "created","registered","updated","failed","canceling" and "canceled".
        - keyIds [list of strings, default null]: list of PixKey IDs to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - enumerator of PixKey\Log objects with updated attributes
      */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, Log::resource(), $options);
    }

    /**
    # Retrieve paged PixKey\Logs

    Receive a list of up to 100 PixKey\Log objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your keys.
    
    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - ids [list of strings, default null]: Log ids to filter PixKey Logs. ex: ["5656565656565656"]
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [list of strings, default null]: filter retrieved objects by types. ex: "created", "registered", "updated", "failed", "canceling" and "canceled".
        - keyIds [list of strings, default null]: list of PixKey IDs to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - list of PixKey.Log objects with updated attributes
        - cursor to retrieve the next page of PixKey\Log objects
     */
    public static function page($options = [], $user = null)
     {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, Log::resource(), $options);
     }

    private static function resource()
    {
        $keyLog = function ($array) {
            $key = function ($array) {
                return new TestPixKeyLog($array);
            };
            $array["key"] = API::fromApiJson($key, $array["key"]);
            return new Log($array);
        };
        return [
            "name" => "PixKeyLog",
            "maker" => $keyLog,
        ];
    }
}
