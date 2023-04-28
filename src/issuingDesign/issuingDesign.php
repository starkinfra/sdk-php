<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\API;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class IssuingDesign extends Resource
{

    public $name;
    public $embosserIds;
    public $type;
    public $updated;
    public $created;

    /**
    # IssuingDesign object

    The IssuingDesign object displays information on the card and card package designs available to your Workspace.

    ## Attributes (return-only):
        - id [string]: unique id returned when the IssuingDesign is created. ex: "5656565656565656"
        - name [string]: card or package design name. ex: "stark-plastic-dark-001"
        - embosserIds [array of strings, default null]: array of embosser unique ids. ex: ["5136459887542272", "5136459887542276"]
        - type [string]: card or package design type. Options: "card", "envelope"
        - updated [DateTime]: updated datetime for the IssuingDesign.
        - created [DateTime]: creation datetime for the IssuingDesign. 
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> name = Checks::checkParam($params, "name");
        $this-> embosserIds = Checks::checkParam($params, "embosserIds");
        $this-> type = Checks::checkParam($params, "type");
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));

        Checks::checkParams($params);
    }

    public static function parseDesigns($designs) {
        if ($designs == null) {
            return null;
        }
        $parsedDesigns = [];
        foreach($designs as $design) {
            if($design instanceof IssuingDesign) {
                array_push($parsedDesigns, $design);
                continue;
            }
            $parsedDesign = function ($array) {
                $designMaker = function ($array) {
                    return new IssuingDesign($array);
                };
                return API::fromApiJson($designMaker, $array);
            };
            array_push($parsedDesigns, $parsedDesign($design));
        }    
        return $parsedDesigns;
    }

    /**
    # Retrieve a specific IssuingDesign

    Receive a single IssuingDesign object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingDesign object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IssuingDesign::resource(), $id);
    }

    /**
    # Retrieve IssuingDesigns

    Receive an enumerator of IssuingDesign objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingDesign objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        return Rest::getList($user, IssuingDesign::resource(), $options);
    }

    /**
    # Retrieve paged IssuingDesigns

    Receive a list of up to 100 IssuingDesign objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of IssuingDesign objects with updated attributes
        - cursor to retrieve the next page of IssuingDesign objects
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, IssuingDesign::resource(), $options);
    }

    /**
    # Retrieve a specific IssuingDesign pdf file

    Receive a single IssuingDesign pdf file generated in the Stark Infra API by passing its id.

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - Issuing Design pdf file
     */
    public static function pdf($id, $user = null)
    {
        return Rest::getContent($user, IssuingDesign::resource(), $id, "pdf");
    }

    private static function resource()
    {
        $design = function ($array) {
            return new IssuingDesign($array);
        };
        return [
            "name" => "IssuingDesign",
            "maker" => $design,
        ];
    }
}
