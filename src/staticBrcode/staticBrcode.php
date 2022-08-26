<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\StarkDate;


class StaticBrcode extends Resource
{
    /**
    # StaticBrcode object

    A StaticBrcode stores account information in the form of a PixKey and can be used to create 
    Pix transactions easily.
    When you initialize a StaticBrcode, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects

    ## Parameters (required):
        - name [string]: receiver's name. ex: "Tony Stark"
        - keyId [string]: receiver's Pixkey id. ex: "+5541999999999"
        - city [string]: receiver's city name. ex: "Rio de Janeiro"

    ## Parameters (optional):
        - amount [integer, default 0]: positive integer that represents the amount in cents of the resulting Pix transaction. ex: 1234 (= R$ 12.34)
        - reconciliationId [string, default null]: id to be used for conciliation of the resulting Pix transaction. ex: "123"
        - tags [array of strings, default []]: array of strings for tagging. ex: ["travel", "food"]

    ## Attributes (return-only):
        - id [string]: id returned on creation, this is the BR code. ex: "00020126360014br.gov.bcb.pix0114+552840092118152040000530398654040.095802BR5915Jamie Lannister6009Sao Paulo620705038566304FC6C"
        - uuid [string]: unique uuid returned when a StaticBrcode is created. ex: "97756273400d42ce9086404fe10ea0d6"
        - url [string]: url to the BR code image. ex: "https://brcode-h.development.starkinfra.com/static-qrcode/97756273400d42ce9086404fe10ea0d6.png"
        - updated [DateTime]: latest update datetime for the StaticBrcode.
        - created [DateTime]: creation datetime for the StaticBrcode.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> name = Checks::checkParam($params, "name");
        $this-> keyId = Checks::checkParam($params, "keyId");
        $this-> city = Checks::checkParam($params, "city");
        $this-> amount = Checks::checkParam($params, "amount");
        $this-> reconciliationId = Checks::checkParam($params, "reconciliationId");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> uuid = Checks::checkParam($params, "uuid");
        $this-> url = Checks::checkParam($params, "url");
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        
        Checks::checkParams($params);
    }

    /**
    # Create StaticBrcode objects

    Create StaticBrcodes in the Stark Infra API

    ## Parameters (optional):
        - brcodes [array of StaticBrcode objects]: StaticBrcode objects to be created in the API.
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - Array of StaticBrcode objects with updated attributes.
     */
    public static function create($brcodes, $user = null)
    {
        return Rest::post($user, StaticBrcode::resource(), $brcodes);
    }

    /**
    # Retrieve a StaticBrcode object

    Retrieve a StaticBrcode object linked to your Workspace in the Stark Infra API using its uuid.
    
    ## Parameters (required):
        - uuid [string]: object unique uuid. ex: "97756273400d42ce9086404fe10ea0d6".
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call

    ## Return:
        - StaticBrcode object that corresponds to the given uuid.
     */
    public static function get($uuid, $user = null)
    {
        return Rest::getId($user, StaticBrcode::resource(), $uuid);
    }

    /**
    # Retrieve StaticBrcode objects

    Receive an enumerator of StaticBrcode objects previously created in the Stark Infra API
    
        ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - uuids [array of strings, default null]: list of uuids to filter retrieved objects. ex: ["97756273400d42ce9086404fe10ea0d6", "12212250d9cd43e68b3b7c474c9b0e36"]
        - tags [array of strings, default null]: list of tags to filter retrieved objects. ex: ["travel", "food"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call

    ## Return:
        - enumerator of StaticBrcode objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getList($user, StaticBrcode::resource(), $options);
    }

    /**
    # Retrieve paged StaticBrcodes
    
    Receive a list of up to 100 StaticBrcode objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.
    
    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - uuids [array of strings, default null]: list of uuids to filter retrieved objects. ex: ["97756273400d42ce9086404fe10ea0d6", "12212250d9cd43e68b3b7c474c9b0e36"]
        - tags [array of strings, default null]: list of tags to filter retrieved objects. ex: ["travel", "food"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - cursor to retrieve the next page of StaticBrcode objects
        - list of StaticBrcode objects with updated attributes
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, StaticBrcode::resource(), $options);
    }

    private static function resource()
    {
        $brcode = function ($array) {
            return new StaticBrcode($array);
        };
        return [
            "name" => "StaticBrcode",
            "maker" => $brcode,
        ];
    }    
}
