<?php

namespace StarkInfra;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;


class IssuingBin extends Resource
{
    /**
    # IssuingBin object

    The IssuingBin object displays the informations of BINs registered to your Workspace.

    ## Attributes (return-only):
        - id [string]: unique BIN number registered within the card network. ex: "53810200"
        - network [string]: card network flag. ex: "mastercard"
        - settlement [string]: settlement type. ex: "credit"
        - category [string]: purchase category. ex: "prepaid"
        - client [string]: client type. ex: "business"
        - created [DateTime]: creation datetime for the IssuingBin.
        - updated [DateTime]: latest update datetime for the IssuingBin.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->network = Checks::checkParam($params, "network");
        $this->settlement = Checks::checkParam($params, "settlement");
        $this->category = Checks::checkParam($params, "category");
        $this->client = Checks::checkParam($params, "client");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Retrieve IssuingBins

    Receive an enumerator of IssuingBin objects previously registered in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of Bin objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        return Rest::getList($user, IssuingBin::resource(), $options);
    }

    /**
    # Retrieve paged IssuingBins

    Receive an array of up to 100 IssuingBin objects previously registered in the Stark Infra API and the cursor to the next page.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - user [Organization/Project object, default null, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - array of IssuingBin objects with updated attributes
        - cursor to retrieve the next page of IssuingBin objects
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, IssuingBin::resource(), $options);
    }

    private static function resource()
    {
        $bin = function ($array) {
            return new IssuingBin($array);
        };
        return [
            "name" => "IssuingBin",
            "maker" => $bin,
        ];
    }
}
