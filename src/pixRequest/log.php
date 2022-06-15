<?php

namespace StarkInfra\PixRequest;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\API;
use StarkInfra\Utils\StarkDate;
use StarkInfra\PixRequest;


class Log extends Resource
{
    /**
    # PixRequest\Log object

    Every time a PixRequest entity is modified, a corresponding PixRequest\Log
    is generated for the entity. This log is never generated by the
    user.

    ## Attributes:
        - id [string]: unique id returned when the log is created. ex: "5656565656565656"
        - request [PixRequest]: PixRequest entity to which the log refers to.
        - errors [array of strings]: array of errors linked to this BoletoPayment event.
        - type [string]: type of the PixRequest event which triggered the log creation. ex: "processing" or "success"
        - created [DateTime]: creation datetime for the log.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> request = Checks::checkParam($params, "request");
        $this-> errors = Checks::checkParam($params, "errors");
        $this-> type = Checks::checkParam($params, "type");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));

        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific Log

    Receive a single Log object previously created by the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - Log object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, Log::resource(), $id);
    }

    /**
    # Retrieve Logs

    Receive an enumerator of Log objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [array of strings, default null]: filter retrieved objects by types. ex: "sent", "denied", "failed", "created", "success", "approved", "credited", "refunded", "processing".
        - requestIds [array of strings, default null]: array of PixRequest ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - enumerator of Log objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, Log::resource(), $options);
    }

    /**
    # Retrieve paged PixRequest\Logs

    Receive a list of up to 100 PixRequesto\Log objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [array of strings, default null]: filter retrieved objects by types. ex: "sent", "denied", "failed", "created", "success", "approved", "credited", "refunded", "processing".
        - requestIds [array of strings, default null]: list of PixRequest ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - list of PixRequest\Log objects with updated attributes
        - cursor to retrieve the next page of PixRequest\Log objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, Log::resource(), $options);
    }

    private static function resource()
    {
        $requestLog = function ($array) {
            $request = function ($array) {
                return new PixRequest($array);
            };
            $array["request"] = API::fromApiJson($request, $array["request"]);
            return new Log($array);
        };
        return [
            "name" => "PixRequestLog",
            "maker" => $requestLog,
        ];
    }
}
