<?php

namespace StarkInfra\IndividualIdentity;
use StarkCore\Utils\API;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;
use StarkInfra\IndividualIdentity;


class Log extends Resource
{

    public $created;
    public $type;
    public $errors;
    public $identity;

    /**
    # IndividualIdentity\Log object

    Every time an IndividualIdentity entity is updated, a corresponding IndividualIdentity\Log
    is generated for the entity. This log is never generated by the
    user, but it can be retrieved to check additional information
    on the IndividualIdentity.

    ## Attributes (return-only):
        - id [string]: unique id returned when the log is created. ex: "5656565656565656"
        - identity [IndividualIdentity]: Individual Identity entity to which the log refers to.
        - errors [array of strings]: array of errors linked to this Individual Identity event
        - type [string]: type of the IndividualIdentity event which triggered the log creation. ex: "created", "canceled", "processing", "failed", "success"
        - created [DateTime]: creation datetime for the log.
     */

    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> type = Checks::checkParam($params, "type");
        $this-> errors = Checks::checkParam($params, "errors");
        $this-> identity = Checks::checkParam($params, "identity");

        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific IndividualIdentity\Log

    Receive a single IndividualIdentity\Log object previously created by the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IndividualIdentity\Log object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, Log::resource(), $id);
    }

    /**
    # Retrieve IndividualIdentity\Logs

    Receive an enumerator of IndividualIdentity\Log objects previously created in the Stark Infra API.
    Use this function instead of page if you want to stream the objects without worrying about cursors and pagination.

    ## Parameters (optional):
        - limit [int, default null]: Maximum number of structs to be retrieved. Unlimited if null. ex: 35
        - after [string, default null]: Date filter for structs created only after specified date.  ex: "2022-11-10"
        - before [string, default null]: Date filter for structs created only before specified date.  ex: "2022-11-10"
        - types [array of strings, default null]: filter for log event types. ex: ["created", "canceled", "processing", "failed", "success"]
        - identityIds [array of strings, default null]: array of IndividualIdentity ids to filter logs. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IndividualIdentity\Log objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getList($user, Log::resource(), $options);
    }

    /**
    # Retrieve paged IndividualIdentity\Logs

    Receive a list of up to 100 IndividualIdentity\Log objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [string, default null]: Date filter for structs created only after specified date.  ex: "2022-11-10"
        - before [string, default null]: Date filter for structs created only before specified date.  ex: "2022-11-10"
        - types [array of strings, default null]: filter for log event types. ex: ["created", "canceled", "processing", "failed", "success"]
        - identityIds [array of strings, default null]: array of IndividualIdentity ids to filter logs. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - list of IndividualIdentity\Log objects with updated attributes
        - cursor to retrieve the next page of IndividualIdentity\Log objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, Log::resource(), $options);
    }

    private static function resource()
    {
        $identityLog = function ($array) {
            $individualIdentity = function ($array) {
                return new IndividualIdentity($array);
            };
            $array["identity"] = API::fromApiJson($individualIdentity, $array["identity"]);
            return new Log($array);
        };
        return [
            "name" => "IndividualIdentityLog",
            "maker" => $identityLog,
        ];
    }
}
