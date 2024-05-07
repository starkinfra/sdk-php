<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class IssuingTokenDesign extends Resource
{

    public $name;
    public $created;
    public $updated;

    /**
    # IssuingTokenDesign object

    The IssuingTokenDesign object displays the information of the token designs created in your Workspace.
    This resource represents the existent designs for the cards which will be tokenized.

    ## Attributes (return-only):
        - name [string]: Design name. ex: "Stark Bank - White Metal"
        - created [DateTime]: creation datetime for the IssuingTokenDesign.
        - updated [DateTime]: latest update datetime for the IssuingTokenDesign.
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->name = Checks::checkParam($params, "name");
        $this->created = Checks::checkParam($params, "created");
        $this->updated = Checks::checkParam($params, "updated");
        
        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific IssuingTokenDesign

    Receive a single IssuingTokenDesign object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingTokenDesign object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IssuingTokenDesign::resource(), $id);
    }

    /**
    # Retrieve IssuingTokenDesigns

    Receive a generator of IssuingTokenDesign objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - ids [array of strings, default [], default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingTokenDesign objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        return Rest::getList($user, IssuingTokenDesign::resource(), $options);
    }

    /**
    # Retrieve paged IssuingTokenDesigns

    Receive a list of up to 100 IssuingTokenDesigns objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - ids [array of strings, default [], default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of IssuingTokenDesign objects with updated attributes
        - cursor to retrieve the next page of IssuingTokenDesign objects
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, IssuingTokenDesign::resource(), $options);
    }

    /**
    # Retrieve a specific IssuingTokenDesign pdf file

    Receive a single IssuingTokenDesign pdf file generated in the Stark Infra API by passing its id.

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - Issuing Token pdf file
     */
    public static function pdf($id, $user = null)
    {
        return Rest::getContent($user, IssuingTokenDesign::resource(), $id, "pdf");
    }

    private static function resource()
    {
        $design = function ($array) {
            return new IssuingTokenDesign($array);
        };
        return [
            "name" => "IssuingTokenDesign",
            "maker" => $design,
        ];
    }
}
