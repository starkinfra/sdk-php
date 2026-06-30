<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkInfra\Ledger\Rule;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class LedgerTransaction extends Resource
{

    public $amount;
    public $ledgerId;
    public $externalId;
    public $source;
    public $balance;
    public $fee;
    public $rules;
    public $metadata;
    public $tags;
    public $created;

    /**
    # LedgerTransaction object

    LedgerTransactions are used to move amounts in and out of a Ledger, updating its balance.
    They can represent a deposit, a withdrawal, a transfer, an adjustment, etc.

    ## Parameters (required):
        - amount [integer]: amount of the transaction. ex: 11234
        - ledgerId [string]: id of the Ledger containing the transaction. ex: "5656565656565656"
        - externalId [string]: string that must be unique among all your LedgerTransactions in a single Ledger. ex: "my-internal-id-123456"
        - source [string]: source of the LedgerTransaction. ex: "bank-transfer/123"

    ## Parameters (optional):
        - fee [integer, default null]: fee applied to the LedgerTransaction. ex: 100
        - rules [array of Ledger\Rule objects, default null]: list of Rule objects linked to the LedgerTransaction. Rules are used to overwrite the Ledger's rules for this transaction. ex: [new Ledger\Rule(["key" => "minimumBalance", "value" => 0])]
        - metadata [dictionary object, default null]: dictionary object used to store additional information about the LedgerTransaction object. ex: ["orderId" => "123", "orderType" => "purchase"]
        - tags [array of strings, default null]: list of strings for reference when searching for LedgerTransactions. ex: ["transfer/123", "savings"]

    ## Attributes (return-only):
        - id [string]: unique id returned when the LedgerTransaction is created. ex: "5656565656565656"
        - balance [integer]: Ledger's balance after the transaction. ex: 11234
        - created [DateTime]: creation datetime for the LedgerTransaction. ex: "2026-04-03T12:00:00+00:00"
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->amount = Checks::checkParam($params, "amount");
        $this->ledgerId = Checks::checkParam($params, "ledgerId");
        $this->externalId = Checks::checkParam($params, "externalId");
        $this->source = Checks::checkParam($params, "source");
        $this->balance = Checks::checkParam($params, "balance");
        $this->fee = Checks::checkParam($params, "fee");
        $this->rules = Rule::parseRules(Checks::checkParam($params, "rules"));
        $this->metadata = Checks::checkParam($params, "metadata");
        $this->tags = Checks::checkParam($params, "tags");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));

        Checks::checkParams($params);
    }

    /**
    # Create LedgerTransactions

    Send a list of LedgerTransaction objects for creation in the Stark Infra API

    ## Parameters (required):
        - transactions [array of LedgerTransaction objects]: list of LedgerTransaction objects to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of LedgerTransaction objects with updated attributes
     */
    public static function create($transactions, $user = null)
    {
        return Rest::post($user, LedgerTransaction::resource(), $transactions);
    }

    /**
    # Retrieve a specific LedgerTransaction

    Receive a single LedgerTransaction object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - LedgerTransaction object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, LedgerTransaction::resource(), $id);
    }

    /**
    # Retrieve LedgerTransactions

    Receive an enumerator of LedgerTransaction objects previously created in the Stark Infra API

    ## Parameters (conditionally required):
        - ledgerId [string, default null]: id of the Ledger containing the transaction. Either ledgerId or ids must be provided. If both are sent, the query will be filtered by both. ex: "5656565656565656"
        - ids [array of strings, default null]: list of ids to filter retrieved objects. Either ledgerId or ids must be provided. If both are sent, the query will be filtered by both. ex: ["5656565656565656", "4545454545454545"]

    ## Parameters (optional):
        - flow [string, default null]: direction of the transaction. ex: "in" or "out"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["transfer/123", "savings"]
        - externalIds [array of strings, default null]: list of external ids to filter retrieved objects. ex: ["my-internal-id-123456", "my-internal-id-654321"]
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - limit [integer, default 100, maximum 1000]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of LedgerTransaction objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, LedgerTransaction::resource(), $options);
    }

    /**
    # Retrieve paged LedgerTransactions

    Receive a list of LedgerTransaction objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (conditionally required):
        - ledgerId [string, default null]: id of the Ledger containing the transaction. Either ledgerId or ids must be provided. If both are sent, the query will be filtered by both. ex: "5656565656565656"
        - ids [array of strings, default null]: list of ids to filter retrieved objects. Either ledgerId or ids must be provided. If both are sent, the query will be filtered by both. ex: ["5656565656565656", "4545454545454545"]

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - flow [string, default null]: direction of the transaction. ex: "in" or "out"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["transfer/123", "savings"]
        - externalIds [array of strings, default null]: list of external ids to filter retrieved objects. ex: ["my-internal-id-123456", "my-internal-id-654321"]
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - limit [integer, default 100, maximum 1000]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of LedgerTransaction objects with updated attributes
        - cursor to retrieve the next page of LedgerTransaction objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, LedgerTransaction::resource(), $options);
    }

    private static function resource()
    {
        $transaction = function ($array) {
            return new LedgerTransaction($array);
        };
        return [
            "name" => "LedgerTransaction",
            "maker" => $transaction,
        ];
    }
}
