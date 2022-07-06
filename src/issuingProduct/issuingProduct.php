<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;


class IssuingProduct extends Resource
{
    /**
    # IssuingProduct object

    The IssuingProduct object displays the informations of BINs registered to your Workspace.

    ## Attributes (return-only):
        - id [string]: unique BIN number registered within the card network. ex: "53810200"
        - network [string]: card network flag. ex: "mastercard"
        - settlement [string]: settlement type. ex: "credit"
        - category [string]: purchase category. ex: "prepaid"
        - client [string]: client type. ex: "business"
        - created [DateTime]: creation datetime for the IssuingProduct.
        - updated [DateTime]: latest update datetime for the IssuingProduct.
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
    # Retrieve IssuingProducts

    Receive an enumerator of IssuingProduct objects previously registered in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingProduct objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        return Rest::getList($user, IssuingProduct::resource(), $options);
    }

    /**
    # Retrieve paged IssuingProducts

    Receive an array of up to 100 IssuingProduct objects previously registered in the Stark Infra API and the cursor to the next page.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - array of IssuingProduct objects with updated attributes
        - cursor to retrieve the next page of IssuingProduct objects
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, IssuingProduct::resource(), $options);
    }

    private static function resource()
    {
        $product = function ($array) {
            return new IssuingProduct($array);
        };
        return [
            "name" => "IssuingProduct",
            "maker" => $product,
        ];
    }
}
