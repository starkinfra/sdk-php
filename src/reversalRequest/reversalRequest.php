<?php

namespace StarkInfra;

use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\StarkDate;

class ReversalRequest extends Resource
{
    /*
    # ReversalRequest object

    A reversal request can be created when fraud is detected on a transaction or a system malfunction
    results in an erroneous transaction.
    It notifies another participant of your request to reverse the payment they have received.
    When you initialize a ReversalRequest, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - amount [integer]: amount in cents to be reversed. ex: 11234 (= R$ 112.34)
        - referenceId [string]: end_to_end_id or return_id of the transaction to be reversed. ex: "E20018183202201201450u34sDGd19lz"
        - reason [string]: reason why the reversal was requested. Options: "fraud", "flaw", "reversalChargeback"

    ## Parameters (optional):
        - description [string, default None]: description for the ReversalRequest.

    ## Attributes (return-only):
        - analysis [string]: analysis that led to the result.   
        - bacenId [string]: central bank's unique UUID that identifies the ReversalRequest.
        - senderBankCode [string]: bank_code of the Pix participant that created the ReversalRequest. ex: "20018183"
        - receiverBankCode [string]: bank_code of the Pix participant that received the ReversalRequest. ex: "20018183"
        - rejectionReason [string]: reason for the rejection of the reversal request. Options: "noBalance", "accountClosed", "unableToReverse"
        - reversalReferenceId [string]: return id of the reversal transaction. ex: "D20018183202202030109X3OoBHG74wo".
        - id [string]: unique id returned when the ReversalRequest is created. ex: "5656565656565656"
        - result [string]: result after the analysis of the ReversalRequest by the receiving party. Options: "rejected", "accepted", "partiallyAccepted"
        - status [string]: current ReversalRequest status. Options: "created", "failed", "delivered", "closed", "canceled".
        - created [datetime.datetime]: creation datetime for the ReversalRequest. ex: datetime.datetime(2020, 3, 10, 10, 30, 0, 0)
        - updated [datetime.datetime]: latest update datetime for the ReversalRequest. ex: datetime.datetime(2020, 3, 10, 10, 30, 0, 0)
         */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->amount = Checks::checkParam($params, "amount");
        $this->referenceId = Checks::checkParam($params, "referenceId");
        $this->reason = Checks::checkParam($params, "reason");
        $this->description = Checks::checkParam($params, "description");
        $this->analysis = Checks::checkParam($params, "analysis");
        $this->bacenId = Checks::checkParam($params, "bacenId");
        $this->sendeBankCode = Checks::checkParam($params, "sendeBankCode");
        $this->receiverBankCode = Checks::checkParam($params, "receiverBankCode");
        $this->rejectionReason = Checks::checkParam($params, "rejectionReason");
        $this->reversalReferenceId = Checks::checkParam($params, "reversalReferenceId");
        $this->id = Checks::checkParam($params, "id");
        $this->result = Checks::checkParam($params, "result");
        $this->status = Checks::checkParam($params, "status");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));
        
        Checks::checkParams($params);
    }

    /*
    # Create a ReversalRequest object

    Create a ReversalRequest in the Stark Infra API

    ## Parameters (optional):
        - request [ReversalRequest object]: ReversalRequest object to be created in the API.
    
    ## Parameters (optional):
        - user [Organization/Project object, default None]: Organization or Project object. Not necessary if starkinfra.user was set before function call
    
    ## Return:
        - ReversalRequest object with updated attributes.
     */

    public static function create($request, $user = null)
    {
        return Rest::postSingle($user, ReversalRequest::resource(), $request);
    }

    /*
    # Retrieve a ReversalRequest object

    Retrieve the ReversalRequest object linked to your Workspace in the Stark Infra API using its id.
    
    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656".
    
    ## Parameters (optional):
        - user [Organization/Project object, default None]: Organization or Project object. Not necessary if starkinfra.user was set before function call

    ## Return:
        - ReversalRequest object that corresponds to the given id.
     */

    public static function get($id, $user = null)
    {
        return Rest::getId($user, ReversalRequest::resource(), $id);
    }

    /*
    # Retrieve ReversalRequests

    Receive a generator of ReversalRequests objects previously created in the Stark Infra API
    
        ## Parameters (optional):
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [datetime.date or string, default None]: date filter for objects created after a specified date. ex: datetime.date(2020, 3, 10)
        - before [datetime.date or string, default None]: date filter for objects created before a specified date. ex: datetime.date(2020, 3, 10)
        - status [list of strings, default None]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "closed", "canceled".
        - ids [list of strings, default None]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default None]: Organization or Project object. Not necessary if starkinfra.user was set before function call

    ## Return:
        - generator of ReversalRequest objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, ReversalRequest::resource(), $options);
    }

    /*
    # Retrieve ReversalRequests
    
    Receive a generator of ReversalRequests objects previously created in the Stark Infra API
    
    ## Parameters (optional):
        - cursor [string, default None]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [datetime.date or string, default None]: date filter for objects created after a specified date. ex: datetime.date(2020, 3, 10)
        - before [datetime.date or string, default None]: date filter for objects created before a specified date. ex: datetime.date(2020, 3, 10)
        - status [list of strings, default None]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "closed", "canceled".
        - ids [list of strings, default None]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [list of strings, default None]: filter for the type of retrieved ReversalRequests. Options: "fraud", "reversal", "reversalChargeback"
        - user [Organization/Project object, default None]: Organization or Project object. Not necessary if starkinfra.user was set before function call
    
    ## Return:
        - cursor to retrieve the next page of ReversalRequest objects
        - generator of ReversalRequest objects with updated attributes
     */

    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, ReversalRequest::resource(), $options);
    }

    /*
    # Update ReversalRequest entity
    
    Respond to a received ReversalRequest.
    
    ## Parameters (required):
        - id [string]: ReversalRequest id. ex: '5656565656565656'
        - result [string]: result after the analysis of the ReversalRequest. Options: "rejected", "accepted", "partiallyAccepted".
    
    ## Parameters (conditionally required):
        - rejectionReason [string, default None]: if the ReversalRequest is rejected a reason is required. Options: "noBalance", "accountClosed", "unableToReverse",
        - reversalReferenceId [string, default None]: return_id of the reversal transaction. ex: "D20018183202201201450u34sDGd19lz"
    
    ## Parameters (optional):
        - analysis [string, default None]: description of the analysis that led to the result.
    
    ## Return:
        - ReversalRequest with updated attributes
    */
    public static function update($id, $options = [], $user=null)
    {
        return Rest::patchId($user, ReversalRequest::resource(), $id, $options);
    }

    /*
    # Delete a ReversalRequest entity
    
    Delete a ReversalRequest entity previously created in the Stark Infra API
    
    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default None]: Organization or Project object. Not necessary if starkinfra.user was set before function call
    
    ## Return:
        - deleted ReversalRequest object
     */

    public static function delete($id, $user = null)
    {
        return Rest::deleteId($user, ReversalRequest::resource(), $id);
    }

    private static function resource()
    {
        $reversal = function ($array) {
            return new ReversalRequest($array);
        };
        return [
            "name" => "ReversalRequest",
            "maker" => $reversal,
        ];
    }    
}