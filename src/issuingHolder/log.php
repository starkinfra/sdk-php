<?php

namespace StarkInfra\IssuingHolder;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\API;
use StarkInfra\Utils\StarkDate;
use StarkInfra\IssuingHolder;


class Log extends Resource
{
    /**
    # IssuingHolder\Log object

    Every time a IssuingHolder entity is updated, a corresponding IssuingHolder\Log
    is generated for the entity. This log is never generated by the
    user, but it can be retrieved to check additional information
    on the IssuingHolder.

    ## Attributes:
        - id [string]: unique id returned when the log is created. ex: "5656565656565656"
        - holder [IssuingHolder]: IssuingHolder entity to which the log refers to.
        - errors [array of strings]: array of errors linked to this IssuingHolder event.
        - type [string]: type of the IssuingHolder event which triggered the log creation. ex: "created", "paid", "canceled" or "overdue"
        - created [string, default null]: creation datetime for the log. ex: "2020-03-10 10:30:00.000"
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->holder = Checks::checkParam($params, "holder");
        $this->errors = Checks::checkParam($params, "errors");
        $this->type = Checks::checkParam($params, "type");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));

        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific IssuingHolder\Log

    Receive a single IssuingHolder\Log object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingHolder\Log object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, Log::resource(), $id);
    }

    /**
    # Retrieve IssuingHolder\Logs

    Receive an enumerator of IssuingHolder\Log objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [array of strings, default null]: filter for log event types. ex: "created", "paid", "canceled" or "overdue"
        - holderIds [array of strings, default null]: array of IssuingHolder ids to filter logs. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingHolder\Log objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, Log::resource(), $options);
    }

    /**
    # Retrieve paged IssuingHolder\Logs

    Receive a list of up to 100 IssuingHolder\Log objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
    - cursor [string, default null]: cursor returned on the previous page function call
    - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
    - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
    - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
    - types [array of strings, default null]: filter for log event types. ex: "created", "paid", "canceled" or "overdue"
    - holderIds [array of strings, default null]: array of IssuingHolder ids to filter logs. ex: ["5656565656565656", "4545454545454545"]
    - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
    - list of IssuingHolder\Log objects with updated attributes
    - cursor to retrieve the next page of IssuingHolder\Log objects
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, Log::resource(), $options);
    }

    private static function resource()
    {
        $holderLog = function ($array) {
            $holder = function ($array) {
                return new IssuingHolder($array);
            };
            $array["holder"] = API::fromApiJson($holder, $array["holder"]);
            return new Log($array);
        };
        return [
            "name" => "IssuingHolderLog",
            "maker" => $holderLog,
        ];
    }
}
