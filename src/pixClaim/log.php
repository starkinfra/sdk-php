<?php

namespace StarkInfra\PixClaim;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\API;
use StarkInfra\Utils\StarkDate;
use StarkInfra\PixClaim;


class Log extends Resource
{
    /**
    # PixClaim\Log object

    Every time a PixClaim entity is modified, a corresponding PixClaim.Log
    is generated for the entity. This log is never generated by the user.
    
    ## Attributes:
        - id [string]: unique id returned when the log is created. ex: "5656565656565656"
        - created [DateTime]: creation datetime for the log.
        - type [string]: type of the PixClaim event which triggered the log creation. ex: "created" or "failed"
        - errors [list of strings]: list of errors linked to this PixClaim event
        - agent [string]: agent that modified the PixClaim resulting in the Log. Options: "claimer", "claimed".
        - reason [string]: reason why the PixClaim was modified, resulting in the Log. Options: "fraud", "userRequested", "accountClosure", "defaultOperation", "reconciliation".
        - claim [PixClaim]: PixClaim entity to which the log refers to.

     */

    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> type = Checks::checkParam($params, "type");
        $this-> errors = Checks::checkParam($params, "errors");
        $this-> agent = Checks::checkParam($params, "agent");
        $this-> reason = Checks::checkParam($params, "reason");
        $this-> claim = Checks::checkParam($params, "claim");

        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific PixClaim\Log

    Receive a single PixClaim\Log object previously created by the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixClaim\Log object with updated attributes

     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, Log::resource(), $id);
    }

    /**
    # Retrieve PixClaim\Logs

    Receive a generator of PixClaim\Log objects previously created in the Stark Infra API

    ## Parameters (optional):
        - ids [list of strings, default null]: Log ids to filter PixClaim Logs. ex: ["5656565656565656"]
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [list of strings, default null]: filter retrieved objects by types. ex: "created", "failed", "delivering", "delivered", "confirming", "confirmed", "success", "canceling" and "canceled".
        - claimIds [list of strings, default null]: list of PixClaim ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if starkinfra\user was set before function call
    
    ## Return:
        - generator of PixClaim\Log objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, Log::resource(), $options);
    }

    /**
    # Retrieve paged PixClaim\Logs

    Receive a list of up to 100 PixClaim\Log objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your claims.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - ids [list of strings, default null]: Log ids to filter PixClaim Logs. ex: ["5656565656565656"]
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - types [list of strings, default null]: filter retrieved objects by types. ex: "created", "failed", "delivering", "delivered", "confirming", "confirmed", "success", "canceling" and "canceled".
        - claimIds [list of strings, default null]: list of PixClaim IDs to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - list of PixClaim\Log objects with updated attributes
        - cursor to retrieve the next page of PixClaim\Log objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, Log::resource(), $options);
    }

    private static function resource()
    {
        $claimLog = function ($array) {
            $claim = function ($array) {
                return new PixClaim($array);
            };
            $array["claim"] = API::fromApiJson($claim, $array["claim"]);
            return new Log($array);
        };
        return [
            "name" => "PixClaimLog",
            "maker" => $claimLog,
        ];
    }
}
