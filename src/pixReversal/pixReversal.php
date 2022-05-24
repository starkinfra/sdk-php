<?php

namespace StarkInfra;
use StarkInfra\Utils\Parse;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\StarkDate;


class PixReversal extends Resource
{
    /**
    # PixReversal object

    When you initialize a PixReversal, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the array of created objects.

    ## Parameters (required):
        - amount [integer]: amount in cents to be reversed from PixRequest. ex: 1234 (= R$ 12.34)
        - externalId [string]: url safe string that must be unique among all your PixReversals. Duplicated external IDs will cause failures. By default, this parameter will block any PixReversal that repeats amount and receiver information on the same date. ex: "my-internal-id-123456"
        - endToEndId [string]: central bank's unique transaction ID. ex: "E79457883202101262140HHX553UPqeq"
        - reason [string]: reason why the PixRequest is being reversed. Options are "bankError", "fraud", "chashierError", "customerRequest"

    ## Parameters (optional):
        - tags [list of strings, default null]: list of strings for reference when searching for PixReversals. ex: ["employees", "monthly"]

    ## Attributes (return-only):
        - id [string]: unique id returned when the PixReversal is created. ex: "5656565656565656".
        - returnId [string]: central bank's unique reversal transaction ID. ex: "D20018183202202030109X3OoBHG74wo".
        - bankCode [string]: code of the bank institution in Brazil. ex: "20018183" or "341"
        - fee [string]: fee charged by this PixReversal. ex: 200 (= R$ 2.00)
        - status [string]: current PixReversal status. ex: "registered" or "paid"
        - flow [string]: direction of money flow. ex: "in" or "out"
        - created [DateTime]: creation datetime for the PixReversal. 
        - updated [DateTime]: latest update datetime for the PixReversal. 
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> amount = Checks::checkParam($params, "amount");
        $this-> externalId = Checks::checkParam($params, "externalId");
        $this-> endToEndId = Checks::checkParam($params, "endToEndId");
        $this-> reason = Checks::checkParam($params, "reason");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> returnId = Checks::checkParam($params, "returnId");
        $this-> bankCode = Checks::checkParam($params, "bankCode");
        $this-> fee = Checks::checkParam($params, "fee");
        $this-> status = Checks::checkParam($params, "status");
        $this-> flow = Checks::checkParam($params, "flow");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create PixReversals

    Send an array of PixReversal objects for creation in the Stark Infra API

    ## Parameters (required):
        - reversals [array of PixReversal objects]: array of PixReversal objects to be created in the API.
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of PixReversal objects with updated attributes
     */
    public static function create($reversals, $user = null)
    {
        return Rest::post($user, PixReversal::resource(), $reversals);
    }

    /**
    # Retrieve a specific PixReversal

    Receive a single PixReversal object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - PixReversal object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, PixReversal::resource(), $id);
    }

    /**
    # Retrieve PixReversals

    Receive an enumerator of PixReversal objects previously created in the Stark Infra API

    ## Parameters (optional):
        - fields [list of strings, default null]: parameters to be retrieved from PixReversal objects. ex: ["amount", "id"]
        - limit [integer, default 100]: maximum number of objects to be retrieved. ex: 35
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "paid" or "registered"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - returnIds [list of strings, default null]: central bank's unique transaction IDs. ex: ["E79457883202101262140HHX553UPqeq", "E79457883202101262140HHX553UPxzx"]
        - externalIds [list of strings, default null]: url safe strings that must be unique among all your PixReversals. Duplicated external IDs will cause failures. By default, this parameter will block any PixReversals that repeats amount and receiver information on the same date. ex: ["my-internal-id-123456", "my-internal-id-654321"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of PixReversal objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, PixReversal::resource(), $options);
    }

    /**
    # Retrieve paged PixReversals

    Receive a list of up to 100 PixReversal objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your reversals.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - fields [list of strings, default null]: parameters to be retrieved from PixReversal objects. ex: ["amount", "id"]
        - limit [integer, default 100]: maximum number of objects to be retrieved. ex: 35
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "paid" or "registered"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - returnIds [list of strings, default null]: central bank's unique transaction IDs. ex: ["E79457883202101262140HHX553UPqeq", "E79457883202101262140HHX553UPxzx"]
        - externalIds [list of strings, default null]: url safe strings that must be unique among all your PixReversals. Duplicated external IDs will cause failures. By default, this parameter will block any PixReversals that repeats amount and receiver information on the same date. ex: ["my-internal-id-123456", "my-internal-id-654321"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of PixReversal objects with updated attributes
        - cursor to retrieve the next page of PixReversal objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, PixReversal::resource(), $options);
    }

    /**
    # Parse PixReversals

    Create a single PixReversal object from a content string received from a handler listening at the request url.
    If the provided digital signature does not check out with the Stark public key, a
    stark.error.InvalidSignatureError will be raised.

    ## Parameters (required):
        - content [string]: response content from request received at user endpoint (not parsed)
        - signature [string]: base-64 digital signature received at response header "Digital-Signature"

    ## Parameters (required):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call

    ## Return:
        - Parsed PixReversal object
     */
    public static function parse($content, $signature, $user = null)
    {
        return Parse::parseAndVerify($content, $signature, PixReversal ::resource(), $user);
    }

    private static function resource()
    {
        $reversal = function ($array) {
            return new PixReversal($array);
        };
        return [
            "name" => "PixReversal",
            "maker" => $reversal,
        ];
    }
}
