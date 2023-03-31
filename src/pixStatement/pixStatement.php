<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class PixStatement extends Resource
{

    public $after;
    public $before;
    public $type;
    public $status;
    public $transactionCount;
    public $created;
    public $updated;

    /**
    # PixStatement object

    The PixStatement object stores information about all the transactions that
    happened on a specific day at your settlment account according to the Central Bank.

    It must be created by the user before it can be accessed.
    This feature is only available for direct participants.

    When you initialize a PixStatement, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - after [Date, Datetime or string]: transactions that happened at this date are stored in the PixStatement, must be the same as before. ex: "2020-04-03"
        - before [Date, Datetime or string]: transactions that happened at this date are stored in the PixStatement, must be the same as after. ex: "2020-04-03"
        - type [string]: types of entities to include in statement. Options: ["interchange", "interchangeTotal", "transaction"]

    ## Attributes (return-only):
        - id [string]: unique id returned when the PixStatement is created. ex: "5656565656565656"
        - status [string]: current PixStatement status. ex: "success" or "failed"
        - transactionCount [integer]: number of transactions that happened during the day that the PixStatement was requested. ex: 11
        - created [DateTime or string]: creation datetime for the PixStatement. 
        - updated [DateTime or string]: latest update datetime for the PixStatement. 
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> after = Checks::checkParam($params, "after");
        $this-> before = Checks::checkParam($params, "before");
        $this-> type = Checks::checkParam($params, "type");
        $this-> status = Checks::checkParam($params, "status");
        $this-> transactionCount = Checks::checkParam($params, "transactionCount");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create PixStatement

    Create a PixStatement linked to your Workspace in the Stark Infra API

    ## Parameters (required):
        - statement [PixStatement object]: PixStatement object to be created in the API.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - PixStatement object with updated attributes
     */
    public static function create($statement, $user = null)
    {
        return Rest::postSingle($user, PixStatement::resource(), $statement);
    }

    /**
    # Retrieve a specific PixStatement 

    Retrieve the PixStatement object linked to your Workspace in the Stark Infra API by its id.

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - PixStatement  object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, PixStatement ::resource(), $id);
    }

    /**
    # Retrieve PixStatements

    Receive an enumerator of PixStatement objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of PixStatement objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        return Rest::getList($user, PixStatement ::resource(), $options);
    }

    /**
    # Retrieve paged PixStatements

    Receive a list of up to 100 PixStatement objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your statements.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of PixStatement objects with updated attributes
        - cursor to retrieve the next page of PixStatement  objects
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, PixStatement ::resource(), $options);
    }

    /**
    # Retrieve a .csv PixStatement

    Retrieve a specific PixStatement by its ID in a .csv file.

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - .zip file containing a PixStatement in .csv format
     */
    public static function csv($id, $user = null)
    {
        return Rest::getContent($user, PixStatement::resource(), $id, "csv");
    }

    private static function resource()
    {
        $statement = function ($array) {
            return new PixStatement ($array);
        };
        return [
            "name" => "PixStatement",
            "maker" => $statement,
        ];
    }
}
