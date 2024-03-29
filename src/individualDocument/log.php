<?php

namespace StarkInfra\IndividualDocument;
use StarkCore\Utils\API;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;
use StarkInfra\IndividualDocument;


class Log extends Resource
{

    public $created;
    public $type;
    public $errors;
    public $document;

    /**
    # IndividualDocument\Log object

    Every time an IndividualDocument entity is updated, a corresponding IndividualDocument\Log
    is generated for the entity. This log is never generated by the
    user, but it can be retrieved to check additional information
    on the IndividualDocument.

    ## Attributes (return-only):
        - id [string]: unique id returned when the log is created. ex: "5656565656565656"
        - document [IndividualDocument]: Individual Document entity to which the log refers to.
        - errors [array of strings]: array of errors linked to this Individual Document event
        - type [string]: type of the IndividualDocument event which triggered the log creation. ex: "blocked", "canceled", "created", "expired", "unblocked", "updated"
        - created [DateTime]: creation datetime for the log.
     */

    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> type = Checks::checkParam($params, "type");
        $this-> errors = Checks::checkParam($params, "errors");
        $this-> document = Checks::checkParam($params, "document");

        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific IndividualDocument\Log

    Receive a single IndividualDocument\Log object previously created by the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IndividualDocument\Log object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, Log::resource(), $id);
    }

    /**
    # Retrieve IndividualDocument\Logs

    Receive an enumerator of IndividualDocument\Log objects previously created in the Stark Infra API.
    Use this function instead of page if you want to stream the objects without worrying about cursors and pagination.

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [array of strings, default null]: filter for log event types. ex: "created", "paid", "canceled" or "overdue"
        - documentIds [array of strings, default null]: array of IndividualDocument ids to filter logs. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IndividualDocument\Log objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getList($user, Log::resource(), $options);
    }

    /**
    # Retrieve paged IndividualDocument\Logs

    Receive a list of up to 100 IndividualDocument\Log objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [array of strings, default null]: filter for log event types. ex: "canceled", "created", "expired", "failed", "refunded", "registered", "sending", "sent", "signed", "success"
        - documentIds [array of strings, default null]: array of Individual Document ids to filter logs. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - list of IndividualDocument\Log objects with updated attributes
        - cursor to retrieve the next page of IndividualDocument\Log objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, Log::resource(), $options);
    }

    private static function resource()
    {
        $documentLog = function ($array) {
            $individualDocument = function ($array) {
                return new IndividualDocument($array);
            };
            $array["document"] = API::fromApiJson($individualDocument, $array["document"]);
            return new Log($array);
        };
        return [
            "name" => "IndividualDocumentLog",
            "maker" => $documentLog,
        ];
    }
}
