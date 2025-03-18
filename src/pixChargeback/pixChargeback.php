<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class PixChargeback extends Resource
{

    public $amount;
    public $referenceId;
    public $reason;
    public $description;
    public $tags;
    public $analysis;
    public $senderBankCode;
    public $receiverBankCode;
    public $rejectionReason;
    public $reversalReferenceId;
    public $result;
    public $flow;
    public $status;
    public $created;
    public $updated;

    /**
    # PixChargeback object

    A PixChargeback can be created when fraud is detected on a transaction or a system malfunction
    results in an erroneous transaction.
    It notifies another participant of your request to reverse the payment they have received.

    When you initialize a PixChargeback, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - amount [integer]: amount in cents to be reversed. ex: 11234 (= R$ 112.34)
        - referenceId [string]: endToEndId or returnId of the transaction to be reversed. ex: "E20018183202201201450u34sDGd19lz"
        - reason [string]: reason why the chargeback was requested. Options: "fraud", "flaw", "reversalChargeback"

    ## Parameters (conditionally required)::
        - description [string, default null]: description for the PixChargeback. Required if reason is "flaw".

    ## Parameters (optional):
        - tags [array of strings, default []]: array of strings for tagging. ex: ["travel", "food"]

    ## Attributes (return-only):
        - id [string]: unique id returned when the PixChargeback is created. ex: "5656565656565656"
        - analysis [string]: analysis that led to the result.   
        - senderBankCode [string]: bankCode of the Pix participant that created the PixChargeback. ex: "20018183"
        - receiverBankCode [string]: bankCode of the Pix participant that received the PixChargeback. ex: "20018183"
        - rejectionReason [string]: reason for the rejection of the Pix chargeback. Options: "noBalance", "accountClosed", "invalidRequest", "unableToReverse"
        - reversalReferenceId [string]: returnId or endToEndId of the chargeback transaction. ex: "D20018183202202030109X3OoBHG74wo".
        - result [string]: result after the analysis of the PixChargeback by the receiving party. Options: "rejected", "accepted", "partiallyAccepted"
        - flow [string]: direction of the Pix Chargeback. Options: "in" for received chargebacks, "out" for chargebacks you requested
        - status [string]: current PixChargeback status. Options: "created", "failed", "delivered", "closed", "canceled".
        - created [DateTime]: created datetime for the PixChargeback. 
        - updated [DateTime]: latest update datetime for the PixChargeback. 
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> amount = Checks::checkParam($params, "amount");
        $this-> referenceId = Checks::checkParam($params, "referenceId");
        $this-> reason = Checks::checkParam($params, "reason");
        $this-> description = Checks::checkParam($params, "description");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> analysis = Checks::checkParam($params, "analysis");
        $this-> senderBankCode = Checks::checkParam($params, "senderBankCode");
        $this-> receiverBankCode = Checks::checkParam($params, "receiverBankCode");
        $this-> rejectionReason = Checks::checkParam($params, "rejectionReason");
        $this-> reversalReferenceId = Checks::checkParam($params, "reversalReferenceId");
        $this-> result = Checks::checkParam($params, "result");
        $this-> flow = Checks::checkParam($params, "flow");
        $this-> status = Checks::checkParam($params, "status");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));
        
        Checks::checkParams($params);
    }

    /**
    # Create PixChargeback objects

    Create PixChargebacks in the Stark Infra API

    ## Parameters (optional):
        - chargebacks [array of PixChargeback objects]: PixChargeback objects to be created in the API.
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - Array of PixChargeback objects with updated attributes.
     */
    public static function create($chargebacks, $user = null)
    {
        return Rest::post($user, PixChargeback::resource(), $chargebacks);
    }

    /**
    # Retrieve a PixChargeback object

    Retrieve a PixChargeback object linked to your Workspace in the Stark Infra API using its id.
    
    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656".
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call

    ## Return:
        - PixChargeback object that corresponds to the given id.
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, PixChargeback::resource(), $id);
    }

    /**
    # Retrieve PixChargeback objects

    Receive an enumerator of PixChargeback objects previously created in the Stark Infra API
    
        ## Parameters (optional):
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "closed", "canceled".
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - flow [string, default null]: direction of the Pix Chargeback. Options: "in" for received chargebacks, "out" for chargebacks you requested
        - tags [array of strings, default null]: filter for tags of retrieved objects. ex: ["travel", "food"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call

    ## Return:
        - enumerator of PixChargeback objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getList($user, PixChargeback::resource(), $options);
    }

    /**
    # Retrieve paged PixChargebacks
    
    Receive a list of up to 100 PixChargeback objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.
    
    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "closed", "canceled".
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - flow [string, default null]: direction of the Pix Chargeback. Options: "in" for received chargebacks, "out" for chargebacks you requested
        - tags [array of strings, default null]: filter for tags of retrieved objects. ex: ["travel", "food"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - cursor to retrieve the next page of PixChargeback objects
        - list of PixChargeback objects with updated attributes
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, PixChargeback::resource(), $options);
    }

    /**
    # Update PixChargeback entity
    
    Respond to a received PixChargeback.
    
    ## Parameters (required):
        - id [string]: PixChargeback id. ex: '5656565656565656'
        - result [string]: result after the analysis of the PixChargeback. Options: "rejected", "accepted", "partiallyAccepted".
    
    ## Parameters (conditionally required):
        - params [dictionary of parameters]:
            - rejectionReason [string, default null]: if the PixChargeback is rejected a reason is required. Options: "noBalance", "accountClosed", "invalidRequest", "unableToReverse",
            - reversalReferenceId [string, default null]: returnId of the chargeback transaction. ex: "D20018183202201201450u34sDGd19lz"
    
    ## Parameters (optional):
        - params [dictionary of optional parameters]:
            - analysis [string, default null]: description of the analysis that led to the result. Required if rejectionReason is "invalidRequest".
    
    ## Return:
        - PixChargeback with updated attributes
    */
    public static function update($id, $result, $params, $user=null)
    {
        $params["result"] = $result;
        return Rest::patchId($user, PixChargeback::resource(), $id, $params);
    }

    /**
    # Cancel a PixChargeback entity
    
    Cancel a PixChargeback entity previously created in the Stark Infra API
    
    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - canceled PixChargeback object
     */
    public static function cancel($id, $user = null)
    {
        return Rest::deleteId($user, PixChargeback::resource(), $id);
    }

    private static function resource()
    {
        $chargeback = function ($array) {
            return new PixChargeback($array);
        };
        return [
            "name" => "PixChargeback",
            "maker" => $chargeback,
        ];
    }    
}
