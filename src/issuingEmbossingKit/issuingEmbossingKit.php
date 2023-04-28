<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class IssuingEmbossingKit extends Resource
{

    public $name;
    public $designs;
    public $updated;
    public $created;

    /**
    # IssuingEmbossingKit object

    The IssuingEmbossingKit object displays information on the embossing kits available to your Workspace.

    ## Attributes (return-only):
        - id [string]: unique id returned when IssuingEmbossingKit is created. ex: "5656565656565656"
        - name [string]: embossing kit name. ex: "stark-plastic-dark-001"
        - designs [list of IssuingDesign objects]: list of IssuingDesign objects. ex: [IssuingDesign(), IssuingDesign()]
        - updated [DateTime]: updated datetime for the IssuingEmbossingKit.
        - created [DateTime]: creation datetime for the IssuingEmbossingKit. 
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> name = Checks::checkParam($params, "name");
        $this-> designs = IssuingDesign::parseDesigns(Checks::checkParam($params, "designs"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));

        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific IssuingEmbossingKit

    Receive a single IssuingEmbossingKit object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingEmbossingKit object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IssuingEmbossingKit::resource(), $id);
    }

    /**
    # Retrieve IssuingEmbossingKits

    Receive an enumerator of IssuingEmbossingKit objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "created", "processing", "success", "failed"
        - designIds [array of strings, default null]: array of designIds to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingEmbossingKit objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        return Rest::getList($user, IssuingEmbossingKit::resource(), $options);
    }

    /**
    # Retrieve paged IssuingEmbossingKits

    Receive a list of up to 100 IssuingEmbossingKit objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "created", "processing", "success", "failed"
        - designIds [array of strings, default null]: array of designIds to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of IssuingEmbossingKit objects with updated attributes
        - cursor to retrieve the next page of IssuingEmbossingKit objects
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, IssuingEmbossingKit::resource(), $options);
    }

    private static function resource()
    {
        $kit = function ($array) {
            return new IssuingEmbossingKit($array);
        };
        return [
            "name" => "IssuingEmbossingKit",
            "maker" => $kit,
        ];
    }
}
