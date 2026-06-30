<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkInfra\Ledger\Rule;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class Ledger extends Resource
{

    public $externalId;
    public $rules;
    public $tags;
    public $metadata;
    public $created;
    public $updated;

    /**
    # Ledger object

    Ledgers are used to track the balance of a given amount by inserting LedgerTransactions to them.
    They can represent a bank account, a digital wallet, an inventory product, etc.

    ## Parameters (required):
        - externalId [string]: string that must be unique among all your Ledgers. ex: "my-internal-id-123456"

    ## Parameters (optional):
        - rules [array of Ledger\Rule objects, default null]: list of Rule objects linked to the Ledger. Rules are used to limit the balance of the Ledger. ex: [new Ledger\Rule(["key" => "minimumBalance", "value" => 0])]
        - tags [array of strings, default null]: list of strings for reference when searching for Ledgers. ex: ["account/123", "savings"]
        - metadata [dictionary object, default null]: dictionary object used to store additional information about the Ledger object. ex: ["accountId" => "123", "accountType" => "savings"]

    ## Attributes (return-only):
        - id [string]: unique id returned when the Ledger is created. ex: "5656565656565656"
        - created [DateTime]: creation datetime for the Ledger. ex: "2026-04-03T12:00:00+00:00"
        - updated [DateTime]: latest update datetime for the Ledger. ex: "2026-04-03T12:00:00+00:00"
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->externalId = Checks::checkParam($params, "externalId");
        $this->rules = Rule::parseRules(Checks::checkParam($params, "rules"));
        $this->tags = Checks::checkParam($params, "tags");
        $this->metadata = Checks::checkParam($params, "metadata");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create Ledgers

    Send a list of Ledger objects for creation in the Stark Infra API

    ## Parameters (required):
        - ledgers [array of Ledger objects]: list of Ledger objects to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of Ledger objects with updated attributes
     */
    public static function create($ledgers, $user = null)
    {
        return Rest::post($user, Ledger::resource(), $ledgers);
    }

    /**
    # Retrieve a specific Ledger

    Receive a single Ledger object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - Ledger object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, Ledger::resource(), $id);
    }

    /**
    # Retrieve Ledgers

    Receive an enumerator of Ledger objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - externalIds [array of strings, default null]: list of external ids to filter retrieved objects. ex: ["my-internal-id-123456", "my-internal-id-654321"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["account/123", "savings"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of Ledger objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, Ledger::resource(), $options);
    }

    /**
    # Retrieve paged Ledgers

    Receive a list of up to 100 Ledger objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - externalIds [array of strings, default null]: list of external ids to filter retrieved objects. ex: ["my-internal-id-123456", "my-internal-id-654321"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["account/123", "savings"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of Ledger objects with updated attributes
        - cursor to retrieve the next page of Ledger objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, Ledger::resource(), $options);
    }

    /**
    # Update Ledger entity

    Update a Ledger by passing id.

    ## Parameters (required):
        - id [string]: Ledger unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - rules [array of Ledger\Rule objects, default null]: list of Rule objects linked to the Ledger. Rules are used to limit the balance of the Ledger. ex: [new Ledger\Rule(["key" => "minimumBalance", "value" => 0])]
        - tags [array of strings, default null]: list of strings for reference when searching for Ledgers. ex: ["account/123", "savings"]
        - metadata [dictionary object, default null]: dictionary object used to store additional information about the Ledger object. ex: ["accountId" => "123", "accountType" => "savings"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - target Ledger with updated attributes
     */
    public static function update($id, $options = [], $user = null)
    {
        return Rest::patchId($user, Ledger::resource(), $id, $options);
    }

    private static function resource()
    {
        $ledger = function ($array) {
            return new Ledger($array);
        };
        return [
            "name" => "Ledger",
            "maker" => $ledger,
        ];
    }
}
