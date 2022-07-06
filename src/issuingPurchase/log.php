<?php

namespace StarkInfra\IssuingPurchase;
use StarkInfra\Utils\API;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\StarkDate;
use StarkInfra\IssuingPurchase;


class Log extends Resource
{
    /**
    # IssuingPurchase\Log object

    Every time an IssuingPurchase entity is updated, a corresponding issuingpurchase.Log
    is generated for the entity. This log is never generated by the
    user, but it can be retrieved to check additional information
    on the IssuingPurchase.

    ## Attributes (return-only):
        - id [string]: unique id returned when the log is created. ex: "5656565656565656"
        - purchase [IssuingPurchase]: IssuingPurchase entity to which the log refers to.
        - issuingTransactionId [string]: transaction ID related to the IssuingPurchase.
        - errors [array of strings]: list of errors linked to this IssuingPurchase event
        - type [string]: type of the IssuingPurchase event which triggered the log creation. ex: "approved" or "denied"
        - created [DateTime]: creation datetime for the IssuingPurchase. 
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->type = Checks::checkParam($params, "type");
        $this->errors = Checks::checkParam($params, "errors");
        $this->purchase = Checks::checkParam($params, "purchase");

        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific IssuingPurchase\Log

    Receive a single IssuingPurchase\Log object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingPurchase\Log object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, Log::resource(), $id);
    }

    /**
    # Retrieve IssuingPurchase\Logs

    Receive an enumerator of IssuingPurchase\Log objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [array of strings, default null]: filter for log event types. ex: "created", "paid", "canceled" or "overdue"
        - purchaseIds [array of strings, default null]: array of IssuingPurchase ids to filter logs. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingPurchase\Log objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, Log::resource(), $options);
    }

    /**
    # Retrieve paged IssuingPurchase\Logs

    Receive a list of up to 100 IssuingPurchase\Log objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [array of strings, default null]: filter for log event types. ex: "created", "paid", "canceled" or "overdue"
        - purchaseIds [array of strings, default null]: array of IssuingPurchase ids to filter logs. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - list of IssuingPurchase\Log objects with updated attributes
        - cursor to retrieve the next page of IssuingPurchase\Log objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, Log::resource(), $options);
    }

    private static function resource()
    {
        $purchaseLog = function ($array) {
            $purchase = function ($array) {
                return new IssuingPurchase($array);
            };
            $array["purchase"] = API::fromApiJson($purchase, $array["purchase"]);
            return new Log($array);
        };
        return [
            "name" => "IssuingPurchaseLog",
            "maker" => $purchaseLog,
        ];
    }
}
