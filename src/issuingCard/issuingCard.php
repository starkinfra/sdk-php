<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\StarkDate;


class IssuingCard extends Resource
{
    /**
    # IssuingCard object

    The IssuingCard object displays the informations of Cards created to your Workspace.

    ## Parameters (required):
        - holderName [string]: card holder name. ex: "Tony Stark"
        - holderTaxId [string]: card holder tax ID. ex: "012.345.678-90"
        - holderExternalId [string] card holder unique id, generated by the user to avoid duplicated holders. ex: "my-entity/123"

    ## Parameters (optional):
        - displayName [string, default null]: card displayed name. ex: "ANTHONY STARK"
        - rules [array of IssuingRule, default []]: [EXPANDABLE] array of card spending rules.
        - binId [string, default null]: BIN ID to which the card is bound. ex: "53810200"
        - tags [array of strings, default []]: array of strings for tagging. ex: ["travel", "food"]
        - streetLine1 [string, default sub-issuer street line 1]: card holder main address. ex: "Av. Paulista, 200"
        - streetLine2 [string, default sub-issuer street line 2]: card holder address complement. ex: "Apto. 123"
        - district [string, default sub-issuer district]: card holder address district / neighbourhood. ex: "Bela Vista"
        - city [string, default sub-issuer city]: card holder address city. ex: "Rio de Janeiro"
        - stateCode [string, default sub-issuer state code]: card holder address state. ex: "GO"
        - zipCode [string, default sub-issuer zip code]: card holder address zip code. ex: "01311-200"

    ## Attributes (return-only):
        - id [string]: unique id returned when IssuingCard is created. ex: "5656565656565656"
        - holderId [string]: card holder unique id. ex: "5656565656565656"
        - type [string]: card type. ex: "virtual"
        - status [string]: current IssuingCard status. ex: "canceled" or "active"
        - number [string]: [EXPANDABLE] masked card number. ex: "1234 5678 1234 5678"
        - securityCode [string]: [EXPANDABLE] masked card verification value (cvv). Expand to unmask the value. ex: "123".
        - expiration [string]: [EXPANDABLE] masked card expiration datetime. 
        - created [DateTime]: creation datetime for the IssuingCard. 
        - updated [DateTime]: latest update datetime for the IssuingCard. 
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->holderName = Checks::checkParam($params, "holderName");
        $this->holderTaxId = Checks::checkParam($params, "holderTaxId");
        $this->holderExternalId = Checks::checkParam($params, "holderExternalId");
        $this->displayName = Checks::checkParam($params, "displayName");
        $this->rules = IssuingRule::parseRules(Checks::checkParam($params, "rules"));
        $this->binId = Checks::checkParam($params, "binId");
        $this->tags = Checks::checkParam($params, "tags");
        $this->streetLine1 = Checks::checkParam($params, "streetLine1");
        $this->streetLine2 = Checks::checkParam($params, "streetLine2");
        $this->district = Checks::checkParam($params, "district");
        $this->city = Checks::checkParam($params, "city");
        $this->stateCode = Checks::checkParam($params, "stateCode");
        $this->zipCode = Checks::checkParam($params, "zipCode");
        $this->holderId = Checks::checkParam($params, "holderId");
        $this->type = Checks::checkParam($params, "type");
        $this->status = Checks::checkParam($params, "status");
        $this->number = Checks::checkParam($params, "number");
        $this->securityCode = Checks::checkParam($params, "securityCode");
        $expiration = Checks::checkParam($params, "expiration");
        if (!is_null($expiration) && str_contains($expiration, "*"))
            $expiration = null;
        $this->expiration = Checks::checkDateTime($expiration);
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create IssuingCards

    Send an array of IssuingCard objects for creation in the Stark Infra API

    ## Parameters (required):
        - cards [array of IssuingCard objects]: array of IssuingCard objects to be created in the API

    ## Parameters (optional):
        - params [dictionary of optional parameters]:
            - expand [array of strings, default null]: fields to to expand information. ex: ["rules", "securityCode", "number", "expiration"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of IssuingCard objects with updated attributes
     */
    public static function create($cards, $params = null, $user = null)
    {
        return Rest::post($user, IssuingCard::resource(), $cards, $params);
    }

    /**
    # Retrieve IssuingCards

    Receive an enumerator of IssuingCard objects previously created in the Stark Infra API

    ## Parameters (optional):
        - status [string, default null]: filter for status of retrieved objects. ex: "paid" or "registered"
        - types [string, default null]: card type. ex: "virtual"
        - holderIds [array of strings]: card holder IDs. ex: ["5656565656565656", "4545454545454545"]
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - expand [array of strings, default []]: fields to to expand information. ex: ["rules", "securityCode", "number", "expiration"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingCard objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getList($user, IssuingCard::resource(), $options);
    }

    /**
    # Retrieve paged IssuingCards

    Receive an array of IssuingCard objects previously created in the Stark Infra API and the cursor to the next page.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - status [string, default null]: filter for status of retrieved objects. ex: "paid" or "registered"
        - types [string, default null]: card type. ex: "virtual"
        - holderIds [array of strings]: card holder IDs. ex: ["5656565656565656", "4545454545454545"]
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"  
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - expand [array of strings, default []]: fields to to expand information. ex: ["rules", "securityCode", "number", "expiration"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - array of IssuingCard objects with updated attributes
        - cursor to retrieve the next page of IssuingCard objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, IssuingCard::resource(), $options);
    }

    /**
    # Retrieve a specific IssuingCard

    Receive a single IssuingCards object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - params [dictionary of optional parameters]:
            - expand [array of strings, default null]: fields to expand information. ex: ["rules", "security_code", "number", "expiration"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingCards object with updated attributes
     */
    public static function get($id, $params=null, $user = null)
    {
        return Rest::getId($user, IssuingCard::resource(), $id, $params);
    }

    /**
    # Update IssuingCard entity

    Update an IssuingCard by passing id.

    ## Parameters (required):
        - id [string]: IssuingCard id. ex: "5656565656565656"

    ## Parameters (optional):
        - status [string]: You may block the IssuingCard by passing 'blocked' in the status
        - displayName [string, default null]: card displayed name
        - rules [array of dictionaries, default null]: array of dictionaries with "amount": int, "currencyCode": string, "id": string, "interval": string, "name": string pairs.
        - tags [array of strings]: array of strings for tagging
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - target IssuingCard with updated attributes
     */
    public static function update($id, $options = [], $user = null)
    {
        return Rest::patchId($user, IssuingCard::resource(), $id, $options);
    }

    /**
    # Cancel an IssuingCard entity

    Cancel an IssuingCard entity previously created in the Stark Infra API

    ## Parameters (required):
        - id [string]: IssuingCard unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - canceled IssuingCard object
     */
    public static function cancel($id, $user = null)
    {
        return Rest::deleteId($user, IssuingCard::resource(), $id);
    }

    private static function resource()
    {
        $card = function ($array) {
            return new IssuingCard($array);
        };
        return [
            "name" => "IssuingCard",
            "maker" => $card,
        ];
    }
}
