<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class IssuingStockRule extends Resource
{

    public $minimumBalance;
    public $stockId;
    public $tags;
    public $emails;
    public $phones;
    public $status;
    public $created;
    public $updated;

    /**
    # IssuingStockRule object

    The IssuingStockRule object defines a notification rule attached to an IssuingStock.
    When the linked stock balance reaches the minimumBalance, the recipients listed in
    emails and phones are notified.

    ## Parameters (required):
        - minimumBalance [integer]: stock balance threshold that triggers a notification. ex: 10000
        - stockId [string]: IssuingStock unique id the rule is linked to. ex: "5136459887542272"

    ## Parameters (optional):
        - tags [array of strings, default null]: list of strings for tagging. ex: ["card", "corporate"]
        - emails [array of strings, default null]: emails notified when the stock reaches the minimum balance. ex: ["john.doe@enterprise.com"]
        - phones [array of strings, default null]: phones notified when the stock reaches the minimum balance. ex: ["+55 (11) 91234 5678"]

    ## Attributes (return-only):
        - id [string]: unique id returned when IssuingStockRule is created. ex: "5664445921492992"
        - status [string]: current IssuingStockRule status. ex: "active", "canceled"
        - created [DateTime]: creation datetime for the IssuingStockRule.
        - updated [DateTime]: latest update datetime for the IssuingStockRule.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->minimumBalance = Checks::checkParam($params, "minimumBalance");
        $this->stockId = Checks::checkParam($params, "stockId");
        $this->tags = Checks::checkParam($params, "tags");
        $this->emails = Checks::checkParam($params, "emails");
        $this->phones = Checks::checkParam($params, "phones");
        $this->status = Checks::checkParam($params, "status");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create IssuingStockRules

    Send an array of IssuingStockRule objects for creation at the Stark Infra API

    ## Parameters (required):
        - rules [array of IssuingStockRule objects]: array of IssuingStockRule objects to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of IssuingStockRule objects with updated attributes
     */
    public static function create($rules, $user = null)
    {
        return Rest::post($user, IssuingStockRule::resource(), $rules);
    }

    /**
    # Retrieve a specific IssuingStockRule

    Receive a single IssuingStockRule object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5664445921492992"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingStockRule object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IssuingStockRule::resource(), $id);
    }

    /**
    # Retrieve IssuingStockRules

    Receive an enumerator of IssuingStockRule objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-03-10"
        - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-03-10"
        - status [array of strings, default null]: filter for status of retrieved objects. ex: ["active", "canceled"]
        - stockIds [array of strings, default null]: array of IssuingStock ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["card", "corporate"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingStockRule objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, IssuingStockRule::resource(), $options);
    }

    /**
    # Retrieve paged IssuingStockRules

    Receive an array of up to 100 IssuingStockRule objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-03-10"
        - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-03-10"
        - status [array of strings, default null]: filter for status of retrieved objects. ex: ["active", "canceled"]
        - stockIds [array of strings, default null]: array of IssuingStock ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["card", "corporate"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of IssuingStockRule objects with updated attributes
        - cursor to retrieve the next page of IssuingStockRule objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, IssuingStockRule::resource(), $options);
    }

    /**
    # Update IssuingStockRule entity

    Update an IssuingStockRule by passing its id.

    ## Parameters (required):
        - id [string]: IssuingStockRule unique id. ex: "5664445921492992"

    ## Parameters (optional):
        - minimumBalance [integer, default null]: stock balance threshold that triggers a notification. ex: 10000
        - tags [array of strings, default null]: list of strings for tagging. ex: ["card", "corporate"]
        - emails [array of strings, default null]: emails notified when the stock reaches the minimum balance. ex: ["john.doe@enterprise.com"]
        - phones [array of strings, default null]: phones notified when the stock reaches the minimum balance. ex: ["+55 (11) 91234 5678"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - target IssuingStockRule with updated attributes
     */
    public static function update($id, $options = [], $user = null)
    {
        return Rest::patchId($user, IssuingStockRule::resource(), $id, $options);
    }

    /**
    # Cancel an IssuingStockRule entity

    Cancel an IssuingStockRule entity previously created in the Stark Infra API

    ## Parameters (required):
        - id [string]: IssuingStockRule unique id. ex: "5664445921492992"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - canceled IssuingStockRule object
     */
    public static function cancel($id, $user = null)
    {
        return Rest::deleteId($user, IssuingStockRule::resource(), $id);
    }

    private static function resource()
    {
        $rule = function ($array) {
            return new IssuingStockRule($array);
        };
        return [
            "name" => "IssuingStockRule",
            "maker" => $rule,
        ];
    }

}
