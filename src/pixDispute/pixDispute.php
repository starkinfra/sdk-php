<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;
use StarkInfra\PixDispute\Transaction;


class PixDispute extends Resource
{

    public $referenceId;
    public $method;
    public $description;
    public $operatorEmail;
    public $operatorPhone;
    public $tags;
    public $minTransactionAmount;
    public $maxTransactionCount;
    public $maxHopInterval;
    public $maxHopCount;
    public $bacenId;
    public $flow;
    public $status;
    public $transactions;
    public $created;
    public $updated;

    /**
    # PixDispute object

    Pix disputes can be created when a fraud is detected creating a chain of transactions
    in order to reverse the funds to the origin. When you initialize a PixDispute,
    the entity will not be automatically created in the Stark Infra API. The 'create'
    function sends the objects to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - referenceId [string]: endToEndId of the transaction being reported. ex: "E20018183202201201450u34sDGd19lz"
        - method [string]: method used to perform the fraudulent action. Options: "scam", "unauthorized", "coercion", "invasion", "other"
        - operatorEmail [string]: contact email of the operator responsible for the dispute.
        - operatorPhone [string]: contact phone number of the operator responsible for the dispute.

    ## Parameters (conditionally required):
        - description [string, default null]: description including any details that can help with the dispute investigation. The description parameter is required when method is "other".

    ## Parameters (optional):
        - tags [array of strings]: list of strings for tagging. ex: ["travel", "food"]
        - minTransactionAmount [integer]: minimum transaction amount to be considered for the graph creation.
        - maxTransactionCount [integer]: maximum number of transactions to be considered for the graph creation.
        - maxHopInterval [integer]: mean time between transactions to be considered for the graph creation.
        - maxHopCount [integer]: depth to be considered for the graph creation.

    ## Attributes (return-only):
        - id [string]: unique id returned when the PixDispute is created. ex: "5656565656565656"
        - bacenId [string]: Central Bank's unique dispute id. ex: "817fc523-9e9d-40ab-9e53-dacb71454a05"
        - flow [string]: indicates the flow of the Pix Dispute. Options: "in" if you received the PixDispute, "out" if you created the PixDispute.
        - status [string]: current PixDispute status. Options: "created", "delivered", "analysed", "processing", "closed", "failed", "canceled".
        - transactions [array of PixDispute.Transaction objects]: list of transactions related to the dispute.
        - created [DateTime]: creation datetime for the PixDispute.
        - updated [DateTime]: latest update datetime for the PixDispute.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->referenceId = Checks::checkParam($params, "referenceId");
        $this->method = Checks::checkParam($params, "method");
        $this->description = Checks::checkParam($params, "description");
        $this->operatorEmail = Checks::checkParam($params, "operatorEmail");
        $this->operatorPhone = Checks::checkParam($params, "operatorPhone");
        $this->tags = Checks::checkParam($params, "tags");
        $this->minTransactionAmount = Checks::checkParam($params, "minTransactionAmount");
        $this->maxTransactionCount = Checks::checkParam($params, "maxTransactionCount");
        $this->maxHopInterval = Checks::checkParam($params, "maxHopInterval");
        $this->maxHopCount = Checks::checkParam($params, "maxHopCount");
        $this->bacenId = Checks::checkParam($params, "bacenId");
        $this->flow = Checks::checkParam($params, "flow");
        $this->status = Checks::checkParam($params, "status");
        $this->transactions = Transaction::parseTransactions(Checks::checkParam($params, "transactions"));
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create PixDisputes
    
    Send an array of PixDispute objects for creation in the Stark Infra API

    ## Parameters (required):
        - disputes [array of PixDispute objects]: array of Pix Dispute objects to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of PixDispute objects with updated attributes
     */
    public static function create($disputes, $user = null)
    {
        return Rest::post($user, PixDispute::resource(), $disputes);
    }

    /**
    # Retrieve a specific Pix Dispute

    Receive a single PixDispute object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - PixDispute object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, PixDispute::resource(), $id);
    }

    /**
    # Retrieve Pix Disputes

    Receive an enumerator of PixDispute objects previously created in the Stark Infra API.
    Use this function instead of page if you want to stream the objects without worrying about cursors and pagination.

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "created", "delivered"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of Pix Dispute objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        
        return Rest::getList($user, PixDispute::resource(), $options);
    }

    /**
    # Retrieve paged Pix Disputes

    Receive a list of up to 100 Pix Dispute objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "canceled", "created", "expired", "failed", "processing", "signed", "success"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - list of Pix Dispute objects with updated attributes
        - cursor to retrieve the next page of Pix Dispute objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, PixDispute::resource(), $options);
    }

    /**
    # Cancel a PixDispute entity

    Cancel a PixDispute entity previously created in the Stark Infra API

    ## Parameters (required):
        - id [string]: Pix Dispute unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - canceled PixDispute object
     */
    public static function cancel($id, $user = null)
    {
        return Rest::deleteId($user, PixDispute::resource(), $id);
    }

    private static function resource()
    {
        $dispute = function ($array) {
            return new PixDispute($array);
        };
        return [
            "name" => "PixDispute",
            "maker" => $dispute,
        ];
    }
}