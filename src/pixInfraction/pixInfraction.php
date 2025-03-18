<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class PixInfraction extends Resource
{

    public $referenceId;
    public $type;
    public $method;
    public $description;
    public $tags;
    public $fraudType;
    public $fraudId;
    public $creditedBankCode;
    public $flow;
    public $analysis;
    public $debitedBankCode;
    public $reportedBy;
    public $result;
    public $status;
    public $created;
    public $updated;
    public $operatorEmail;
    public $operatorPhone;

    /**
    # PixInfraction object

    Pix Infractions are used to report transactions that are suspected of
    fraud, to request a refund or to reverse a refund.
    When you initialize a PixInfraction, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the created object.
    
    ## Parameters (required):
        - referenceId [string]: endToEndId or returnId of the transaction being reported. ex: "E20018183202201201450u34sDGd19lz"
        - type [string]: type of Pix infraction. Options: "reversal", "reversalChargeback"
        - method [string]: method of Pix Infraction. Options: "scam", "unauthorized", "coercion", "invasion", "other"

    ## Parameters (optional):
        - description [string, default null]: description for any details that can help with the infraction investigation.
        - tags [array of strings, default []]: array of strings for tagging. ex: ["travel", "food"]
        - fraudType [string, default null]: type of Pix Fraud. Options: "identity", "mule", "scam", "other"
        - operatorEmail [string, default null]: contact email of the operator responsible for the PixInfraction.
        - operatorPhone [string, default null]: contact phone number of the operator responsible for the PixInfraction.
    
    ## Attributes (return-only):
        - id [string]: unique id returned when the PixInfraction is created. ex: "5656565656565656"
        - fraudId [string]: id of the Pix Fraud. ex: "5741774970552320"
        - creditedBankCode [string]: bankCode of the credited Pix participant in the reported transaction. ex: "20018183"
        - debitedBankCode [string]: bankCode of the debited Pix participant in the reported transaction. ex: "20018183"
        - flow [string]: direction of the PixInfraction flow. Options: "out" if you created the PixInfraction, "in" if you received the PixInfraction.
        - analysis [string]: analysis that led to the result.
        - reportedBy [string]: agent that reported the PixInfraction. Options: "debited", "credited".
        - result [string]: result after the analysis of the PixInfraction by the receiving party. Options: "agreed", "disagreed"
        - status [string]: current PixInfraction status. Options: "created", "failed", "delivered", "closed", "canceled".
        - created [DateTime]: created datetime for the PixInfraction. 
        - updated [DateTime]: latest update datetime for the PixInfraction. 
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> referenceId = Checks::checkParam($params, "referenceId");
        $this-> type = Checks::checkParam($params, "type");
        $this-> method=Checks::checkParam($params, "method");
        $this-> description = Checks::checkParam($params, "description");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> fraudType = Checks::checkParam($params, "fraudType");
        $this-> operatorEmail = Checks::checkParam($params, "operatorEmail");
        $this-> operatorPhone = Checks::checkParam($params, "operatorPhone");
        $this-> fraudId = Checks::checkParam($params, "fraudId");
        $this-> creditedBankCode = Checks::checkParam($params, "creditedBankCode");
        $this-> flow = Checks::checkParam($params, "flow");
        $this-> analysis = Checks::checkParam($params, "analysis");
        $this-> debitedBankCode = Checks::checkParam($params, "debitedBankCode");
        $this-> reportedBy = Checks::checkParam($params, "reportedBy");
        $this-> result = Checks::checkParam($params, "result");
        $this-> status = Checks::checkParam($params, "status");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create PixInfraction objects

    Create PixInfraction objects in the Stark Infra API
    
    ## Parameters (optional):
        - infractions [array of PixInfraction objects]: array of PixInfraction objects to be created in the API.
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - array of PixInfraction objects with updated attributes
    */
    public static function create($infractions, $user=null)
    {
        return Rest::post($user, PixInfraction::resource(), $infractions);
    }

    /** 
    # Retrieve a PixInfraction object

    Retrieve the PixInfraction object linked to your Workspace in the Stark Infra API using its id.

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656".
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixInfraction object that corresponds to the given id.
    */
    public static function get($id, $user=null)
    {
        return Rest::getId($user, PixInfraction::resource(), $id);
    }

    /** 
    # Retrieve PixInfraction objects
    
    Receive an enumerator of PixInfraction objects previously created in the Stark Infra API
    
    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "closed", "canceled".
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [array of strings, default null]: filter for the type of retrieved PixInfraction. Options: "reversal", "reversalChargeback"
        - flow [string, default null]: direction of the PixInfraction flow. Options: "out" if you created the PixInfraction, "in" if you received the PixInfraction.
        - tags [array of strings, default null]: array of strings for tagging. ex: ["travel", "food"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - enumerator of PixInfraction objects with updated attributes
    */
    public static function query($options=[], $user=null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getList($user, PixInfraction::resource(), $options);
    }

    /** 
    # Retrieve paged PixInfractions

    Receive a list of up to 100 PixInfraction objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.
    
    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "closed", "canceled".
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [array of strings, default null]: filter for the type of retrieved PixInfraction. Options: "reversal", "reversalChargeback"
        - flow [string, default null]: direction of the PixInfraction flow. Options: "out" if you created the PixInfraction, "in" if you received the PixInfraction.
        - tags [array of strings, default null]: array of strings for tagging. ex: ["travel", "food"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - list of PixInfraction objects with updated attributes
        - cursor to retrieve the next page of PixInfraction objects
    */
    public static function page($options = [], $user=null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, PixInfraction::resource(), $options);
    }

    /**
    # Update PixInfraction entity

    Respond to a received PixInfraction.
    
    ## Parameters (required):
        - id [string]: PixInfraction id. ex: '5656565656565656'
        - result [string]: result after the analysis of the PixInfraction. Options: "agreed", "disagreed"
        - fraudType [string]: type of Pix Fraud. Options: "identity", "mule", "scam", "other"
    
    ## Parameters (optional):
        - params [dictionary of optional parameters]:
        - analysis [string, default null]: analysis that led to the result.
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixInfraction with updated attributes
    */
    public static function update($id, $result, $fraudType, $params = [], $user=null)
    {
        $params["result"] = $result;
        $params["fraudType"] = $fraudType;
        return Rest::patchId($user, PixInfraction::resource(), $id, $params);
    }

    /**
    # Cancel a PixInfraction entity
    
    Cancel a PixInfraction entity previously created in the Stark Infra API
    
    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - canceled PixInfraction object
    */
    public static function cancel($id, $user=null)
    {
        return Rest::deleteId($user, PixInfraction::resource(), $id);
    }

    private static function resource()
    {
        $infraction = function ($array) {
            return new PixInfraction($array);
        };
        return [
            "name" => "PixInfraction",
            "maker" => $infraction,
        ];
    }
}
