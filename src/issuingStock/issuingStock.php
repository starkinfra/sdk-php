<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class IssuingStock extends Resource
{

    public $id;
    public $balance;
    public $designId;
    public $embosserId;
    public $updated;
    public $created;

    /**
    # IssuingStock object

    The IssuingStock object represents the current stock of a certain IssuingDesign linked to an Embosser available to your workspace.

    ## Attributes (return-only):
        - id [string]: unique id returned when IssuingStock is created. ex: "5656565656565656"
        - balance [integer]: [EXPANDABLE] current stock balance. ex: 1000
        - designId [string]: IssuingDesign unique id. ex: "5656565656565656"
        - embosserId [string]: Embosser unique id. ex: "5656565656565656"
        - created [DateTime]: creation datetime for the IssuingStock.
        - updated [DateTime]: latest update datetime for the IssuingStock.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->balance = Checks::checkParam($params, "balance");
        $this->designId = Checks::checkParam($params, "designId");
        $this->embosserId = Checks::checkParam($params, "embosserId");
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));

        Checks::checkParams($params);
    }

    /**
    # Retrieve IssuingStocks

    Receive an enumerator of IssuingStock objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-03-10"
        - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-03-10"
        - designIds [array of string, default null]: IssuingDesign unique ids. ex: ["5656565656565656", "4545454545454545"]
        - embosserIds [array of string, default null]: Embosser unique ids. ex: ["5656565656565656", "4545454545454545"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - expand [array of strings, default null]: fields to expand information. ex: ["balance"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingStock objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, IssuingStock::resource(), $options);
    }

    /**
    # Retrieve paged IssuingStocks

    Receive an array of up to 100 IssuingStock objects previously registered in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-03-10"
        - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-03-10"
        - designIds [array of string, default null]: IssuingDesign unique ids. ex: ["5656565656565656", "4545454545454545"]
        - embosserIds [array of string, default null]: Embosser unique ids. ex: ["5656565656565656", "4545454545454545"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - expand [array of strings, default null]: fields to expand information. ex: ["balance"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - array of IssuingStock objects with updated attributes
        - cursor to retrieve the next page of IssuingStock objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, IssuingStock::resource(), $options);
    }

    /**
    # Retrieve a specific IssuingStock

    Receive a single IssuingStock object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - params [dictionary of optional parameters]:
            - expand [array of strings, default null]: fields to expand information. ex: ["balance"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingStock object with updated attributes
     */
    public static function get($id, $params = null, $user = null)
    {
        return Rest::getId($user, IssuingStock::resource(), $id, $params);
    }

    private static function resource()
    {
        $stock = function ($array) {
            return new IssuingStock($array);
        };
        return [
            "name" => "IssuingStock",
            "maker" => $stock,
        ];
    }

}
