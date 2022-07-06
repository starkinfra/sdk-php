<?php

namespace StarkInfra\IssuingInvoice;
use StarkInfra\Utils\API;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\StarkDate;
use StarkInfra\IssuingInvoice;


class Log extends Resource
{
    /**
    # IssuingInvoice\Log object

    Every time an IssuingInvoice entity is updated, a corresponding IssuingInvoice\Log
    is generated for the entity. This log is never generated by the
    user, but it can be retrieved to check additional information
    on the IssuingInvoice.

    ## Attributes (return-only):
        - id [string]: unique id returned when the log is created. ex: "5656565656565656"
        - invoice [Invoice]: IssuingInvoice entity to which the log refers to.
        - type [string]: type of the IssuingInvoice event which triggered the log creation. ex: "created", "paid", "canceled" or "overdue"
        - created [DateTime]: creation datetime for the log. 
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->type = Checks::checkParam($params, "type");
        $this->invoice = Checks::checkParam($params, "invoice");

        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific IssuingInvoice\Log

    Receive a single IssuingInvoice\Log object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingInvoice\Log object with updated attributes
     */

    public static function get($id, $user = null)
    {
        return Rest::getId($user, Log::resource(), $id);
    }

    /**
    # Retrieve IssuingInvoice\Logs

    Receive an enumerator of IssuingInvoice\Log objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [array of strings, default null]: filter for log event types. ex: "created", "paid", "canceled" or "overdue"
        - ids [array of strings, default null]: array of IssuingInvoice ids to filter logs. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingInvoice\Log objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, Log::resource(), $options);
    }

    /**
    # Retrieve paged IssuingInvoice\Logs

    Receive a list of up to 100 IssuingInvoice\Log objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date.  ex: "2020-04-03"
        - types [array of strings, default null]: filter for log event types. ex: "created", "paid", "canceled" or "overdue"
        - ids [array of strings, default null]: array of IssuingInvoice ids to filter logs. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - list of IssuingInvoice\Log objects with updated attributes
        - cursor to retrieve the next page of IssuingInvoice\Log objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, Log::resource(), $options);
    }

    private static function resource()
    {
        $invoiceLog = function ($array) {
            $invoice = function ($array) {
                return new IssuingInvoice($array);
            };
            $array["invoice"] = API::fromApiJson($invoice, $array["invoice"]);
            return new Log($array);
        };
        return [
            "name" => "IssuingInvoiceLog",
            "maker" => $invoiceLog,
        ];
    }
}
