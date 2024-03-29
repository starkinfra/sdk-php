<?php

namespace StarkInfra\CreditHolmes;
use StarkCore\Utils\API;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;
use StarkInfra\CreditHolmes;


class Log extends Resource
{

    public $created;
    public $type;
    public $errors;
    public $holmes;

    /**
    # CreditHolmes\Log object

    Every time a CreditHolmes entity is updated, a corresponding CreditHolmes\Log
    is generated for the entity. This log is never generated by the
    user, but it can be retrieved to check additional information
    on the CreditHolmes.

    ## Attributes (return-only):
        - id [string]: unique id returned when the log is created. ex: "5656565656565656"
        - holmes [CreditHolmes]: Credit Holmes entity to which the log refers to.
        - errors [array of strings]: array of errors linked to this Credit Holmes event
        - type [string]: type of the Credit Holmes event which triggered the log creation. ex: "canceled", "created", "expired", "failed", "refunded", "registered", "sending", "sent", "signed", "success"
        - created [DateTime]: creation datetime for the log.
     */

    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> type = Checks::checkParam($params, "type");
        $this-> errors = Checks::checkParam($params, "errors");
        $this-> holmes = Checks::checkParam($params, "holmes");

        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific CreditHolmes\Log

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
    # Retrieve CreditHolmes\Logs

    Receive an enumerator of CreditHolmes\Log objects previously created in the Stark Infra API.

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [array of strings, default null]: filter for log event types. Options: "canceled", "created", "expired", "failed", "refunded", "registered", "sending", "sent", "signed" or "success"
        - holmesIds [array of strings, default null]: array of CreditHolmes ids to filter logs. ex: ["5656565656565656", "4545454545454545"]
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
    # Retrieve paged CreditHolmes\Logs

    Receive a list of up to 100 CreditHolmes\Log objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [array of strings, default null]: filter for log event types. Options: "canceled", "created", "expired", "failed", "refunded", "registered", "sending", "sent", "signed" or "success"
        - holmesIds [array of strings, default null]: array of Credit Holmes ids to filter logs. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - list of CreditHolmes\Log objects with updated attributes
        - cursor to retrieve the next page of CreditHolmes\Log objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, Log::resource(), $options);
    }

    private static function resource()
    {
        $holmesLog = function ($array) {
            $creditHolmes = function ($array) {
                return new CreditHolmes($array);
            };
            $array["holmes"] = API::fromApiJson($creditHolmes, $array["holmes"]);
            return new Log($array);
        };
        return [
            "name" => "CreditHolmesLog",
            "maker" => $holmesLog,
        ];
    }
}
