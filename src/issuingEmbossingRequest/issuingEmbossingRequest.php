<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class IssuingEmbossingRequest extends Resource
{

    public $cardId;
    public $cardDesignId;
    public $envelopeDesignId;
    public $displayName1;
    public $shippingCity;
    public $shippingCountryCode;
    public $shippingDistrict;
    public $shippingStateCode;
    public $shippingStreetLine1;
    public $shippingStreetLine2;
    public $shippingService;
    public $shippingTrackingNumber;
    public $shippingZipCode;
    public $embosserId;
    public $displayName2;
    public $displayName3;
    public $shippingPhone;
    public $tags;
    public $fee;
    public $status;
    public $created;
    public $updated;

    /**
    # IssuingEmbossingRequest object

    The IssuingEmbossingRequest object displays the information of embossing requests in your Workspace.

    ## Parameters (required):
        - cardId [string]: Id of the IssuingCard to be embossed. ex "5656565656565656"
        - cardDesignId [string]: Card IssuingDesign id. ex "5656565656565656"
        - envelopeDesignId [string]: Envelope IssuingDesign id. ex "5656565656565656"
        - displayName1 [string]: Card displayed name. ex: "ANTHONY STARK"
        - shippingCity [string]: Shipping city. ex: "NEW YORK"
        - shippingCountryCode [string]: Shipping country code. ex: "US"
        - shippingDistrict [string]: Shipping district. ex: "NY"
        - shippingStateCode [string]: Shipping state code. ex: "NY"
        - shippingStreetLine1 [string]: Shipping main address. ex: "AVENUE OF THE AMERICAS"
        - shippingStreetLine2 [string]: Shipping address complement. ex: "Apt. 6"
        - shippingService [string]: Shipping service. ex: "loggi"
        - shippingTrackingNumber [string]: Shipping tracking number. ex: "5656565656565656"
        - shippingZipCode [string]: Shipping zip code. ex: "12345-678"

    ## Parameters (optional):
        - embosserId [string, default null]: Id of the card embosser. ex: "5656565656565656"
        - displayName2 [string, default null]: Card displayed name. ex: "IT Services"
        - displayName3 [string, default null]: Card displayed name. ex: "StarkBank S.A."
        - shippingPhone [string, default null]: Shipping phone. ex: "+5511999999999"
        - tags [array of string, default null]: Slice of strings for tagging. ex: ["card", "corporate"]

    ## Attributes (return-only):
        - id [string]: Unique id returned when IssuingEmbossingRequest is created. ex: "5656565656565656"
        - fee [string]: Fee charged when IssuingEmbossingRequest is created. ex: 1000
        - status [string]: Status of the IssuingEmbossingRequest. ex: "created", "processing", "success", "failed"
        - updated [DateTime]: Latest update datetime for the IssuingEmbossingRequest. 
        - created [DateTime]: Creation datetime for the IssuingEmbossingRequest. 
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->cardId = Checks::checkParam($params, "cardId");
        $this->cardDesignId = Checks::checkParam($params, "cardDesignId");
        $this->envelopeDesignId = Checks::checkParam($params, "envelopeDesignId");
        $this->displayName1 = Checks::checkParam($params, "displayName1");
        $this->shippingCity = Checks::checkParam($params, "shippingCity");
        $this->shippingCountryCode = Checks::checkParam($params, "shippingCountryCode");
        $this->shippingDistrict = Checks::checkParam($params, "shippingDistrict");
        $this->shippingStateCode = Checks::checkParam($params, "shippingStateCode");
        $this->shippingStreetLine1 = Checks::checkParam($params, "shippingStreetLine1");
        $this->shippingStreetLine2 = Checks::checkParam($params, "shippingStreetLine2");
        $this->shippingService = Checks::checkParam($params, "shippingService");
        $this->shippingTrackingNumber = Checks::checkParam($params, "shippingTrackingNumber");
        $this->shippingZipCode = Checks::checkParam($params, "shippingZipCode");
        $this->embosserId = Checks::checkParam($params, "embosserId");
        $this->displayName2 = Checks::checkParam($params, "displayName2");
        $this->displayName3 = Checks::checkParam($params, "displayName3");
        $this->shippingPhone = Checks::checkParam($params, "shippingPhone");
        $this->tags = Checks::checkParam($params, "tags");
        $this->fee = Checks::checkParam($params, "fee");
        $this->status = Checks::checkParam($params, "status");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create IssuingEmbossingRequests

    Send an array of IssuingEmbossingRequest objects for creation in the Stark Infra API

    ## Parameters (required):
        - requests [array of IssuingEmbossingRequest objects]: array of IssuingEmbossingRequest objects to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of IssuingEmbossingRequest objects with updated attributes
     */
    public static function create($requests, $params = null, $user = null)
    {
        return Rest::post($user, IssuingEmbossingRequest::resource(), $requests, $params);
    }

    /**
    # Retrieve IssuingEmbossingRequests

    Receive an enumerator of IssuingEmbossingRequest objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "created", "processing", "success", "failed"
        - cardIds [array of strings, default null]: array of cardIds to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingEmbossingRequest objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getList($user, IssuingEmbossingRequest::resource(), $options);
    }

    /**
    # Retrieve paged IssuingEmbossingRequests

    Receive an array of IssuingEmbossingRequest objects previously created in the Stark Infra API and the cursor to the next page.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "created", "processing", "success", "failed"
        - cardIds [array of strings, default null]: array of cardIds to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - array of IssuingEmbossingRequest objects with updated attributes
        - cursor to retrieve the next page of IssuingEmbossingRequest objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, IssuingEmbossingRequest::resource(), $options);
    }

    /**
    # Retrieve a specific IssuingEmbossingRequest

    Receive a single IssuingEmbossingRequest object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingEmbossingRequest object with updated attributes
     */
    public static function get($id, $params=null, $user = null)
    {
        return Rest::getId($user, IssuingEmbossingRequest::resource(), $id, $params);
    }

    private static function resource()
    {
        $request = function ($array) {
            return new IssuingEmbossingRequest($array);
        };
        return [
            "name" => "IssuingEmbossingRequest",
            "maker" => $request,
        ];
    }
}
