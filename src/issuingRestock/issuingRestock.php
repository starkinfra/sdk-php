<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class IssuingRestock extends Resource
{

    public $count;
    public $stockId;
    public $tags;
    public $status;
    public $updated;
    public $created;

    /**
    # IssuingRestock object

    The IssuingRestock object displays the information of the restock orders created in your Workspace. 

    This resource place a restock order for a specific IssuingStock object.

    ## Parameter (required):
        - count [integer]: number of restocks to be restocked. ex: 100
        - stockId [string]: IssuingStock unique id ex: "5136459887542272"

    ## Parameter (optional):
        - tags [aray of strings, default null]: list of strings for tagging. ex: ["card", "corporate"]

    ## Attributes (return-only):
        - id [string]: unique id returned when IssuingRestock is created. ex: "5656565656565656"
        - status [string]: current IssuingRestock status. ex: "created", "processing", "confirmed"
        - created [DateTime]: creation datetime for the IssuingRestock.
        - updated [DateTime]: latest update datetime for the IssuingRestock.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->count = Checks::checkParam($params, "count");
        $this->stockId = Checks::checkParam($params, "stockId");
        $this->tags = Checks::checkParam($params, "tags");
        $this->status = Checks::checkParam($params, "status");
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));

        Checks::checkParams($params);
    }

    /**
    # Create IssuingRestocks

    Send an array of IssuingRestock objects for creation at the Stark Infra API

    ## Parameters (required):
        - restocks [array of IssuingRestock objects]: array of IssuingRestock objects to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of IssuingRestock objects with updated attributes
     */
    public static function create($restocks, $user = null)
    {
        return Rest::post($user, IssuingRestock::resource(), $restocks);
    }

    /**
    # Retrieve IssuingRestocks

    Receive an enumerator of IssuingRestock objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-03-10"
        - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-03-10"
        - status [array of strings, default null]: filter for status of retrieved objects. ex: ["created", "processing", "confirmed"]
        - stockIds [array of string, default null]: array of stockIds to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["card", "corporate"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingRestock objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, IssuingRestock::resource(), $options);
    }

    /**
    # Retrieve paged IssuingRestocks

    Receive an array of up to 100 IssuingRestock objects previously registered in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-03-10"
        - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-03-10"
        - status [array of strings, default null]: filter for status of retrieved objects. ex: ["created", "processing", "confirmed"]
        - stockIds [array of string, default null]: array of stockIds to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["card", "corporate"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - array of IssuingRestock objects with updated attributes
        - cursor to retrieve the next page of IssuingRestock objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, IssuingRestock::resource(), $options);
    }

    /**
    # Retrieve a specific IssuingRestock

    Receive a single IssuingRestock object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingRestock object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IssuingRestock::resource(), $id);
    }

    private static function resource()
    {
        $restock = function ($array) {
            return new IssuingRestock($array);
        };
        return [
            "name" => "IssuingRestock",
            "maker" => $restock,
        ];
    }

}
