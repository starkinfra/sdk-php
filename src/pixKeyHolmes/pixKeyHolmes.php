<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class PixKeyHolmes extends Resource
{

    public $keyId;
    public $tags;
    public $result;
    public $status;
    public $created;
    public $updated;

    /**
    # PixKeyHolmes object

    A PixKeyHolmes is used to investigate the registration status of a Pix Key
    in the Central Bank's DICT. You open one per key you want to check; the API
    resolves it asynchronously and reports back whether the key is registered.

    When you initialize a PixKeyHolmes, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the array of created objects.

    ## Parameters (required):
        - keyId [string]: Pix Key to be investigated. ex: "+5511989898989", "11.222.333/0001-00", "valid@sandbox.com"

    ## Parameters (optional):
        - tags [array of strings, default []]: Array of strings for reference when searching for PixKeyHolmes. ex: ["employees", "monthly"]

    ## Attributes (return-only):
        - id [string]: Unique id returned when the PixKeyHolmes is created. ex: "5656565656565656"
        - result [string]: Result of the investigation after the case is solved. ex: "registered", "unregistered"
        - status [string]: Current status of the PixKeyHolmes. ex: "created", "solving", "solved", "failed"
        - created [DateTime]: Creation datetime for the PixKeyHolmes.
        - updated [DateTime]: Latest update datetime for the PixKeyHolmes.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> keyId = Checks::checkParam($params, "keyId");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> result = Checks::checkParam($params, "result");
        $this-> status = Checks::checkParam($params, "status");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create PixKeyHolmes

    Send an array of PixKeyHolmes objects for creation in the Stark Infra API

    ## Parameters (required):
        - holmes [array of PixKeyHolmes objects]: array of PixKeyHolmes objects to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of PixKeyHolmes objects with updated attributes
     */
    public static function create($holmes, $user = null)
    {
        return Rest::post($user, PixKeyHolmes::resource(), $holmes);
    }

    /**
    # Retrieve PixKeyHolmes

    Receive an enumerator of PixKeyHolmes objects previously created in the Stark Infra API.

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. ex: ["created", "solving", "solved", "failed"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of PixKeyHolmes objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getList($user, PixKeyHolmes::resource(), $options);
    }

    /**
    # Retrieve paged PixKeyHolmes

    Receive a list of up to 100 PixKeyHolmes objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. ex: ["created", "solving", "solved", "failed"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of PixKeyHolmes objects with updated attributes
        - cursor to retrieve the next page of PixKeyHolmes objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, PixKeyHolmes::resource(), $options);
    }

    private static function resource()
    {
        $holmes = function ($array) {
            return new PixKeyHolmes($array);
        };
        return [
            "name" => "PixKeyHolmes",
            "maker" => $holmes,
        ];
    }
}
