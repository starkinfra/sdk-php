<?php

namespace StarkInfra;

use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\StarkDate;

class PixChargeback extends Resource
{
    /**
    # PixChargeback object

    A charge back request can be created when fraud is detected on a transaction or a system malfunction
    results in an erroneous transaction.
    It notifies another participant of your request to reverse the payment they have received.
    When you initialize a PixChargeback, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - amount [integer]: amount in cents to be reversed. ex: 11234 (= R$ 112.34)
        - referenceId [string]: endToEndId or returnId of the transaction to be reversed. ex: "E20018183202201201450u34sDGd19lz"
        - reason [string]: reason why the chargeback was requested. Options: "fraud", "flaw", "reversalChargeback"

    ## Parameters (optional):
        - description [string, default null]: description for the PixChargeback.

    ## Attributes (return-only):
        - analysis [string]: analysis that led to the result.   
        - bacenId [string]: central bank's unique UUID that identifies the PixChargeback.
        - senderBankCode [string]: bankCode of the Pix participant that created the PixChargeback. ex: "20018183"
        - receiverBankCode [string]: bankCode of the Pix participant that received the PixChargeback. ex: "20018183"
        - rejectionReason [string]: reason for the rejection of the Pix chargeback. Options: "noBalance", "accountClosed", "unableToReverse"
        - chargebackReferenceId [string]: return id of the chargeback transaction. ex: "D20018183202202030109X3OoBHG74wo".
        - id [string]: unique id returned when the PixChargeback is created. ex: "5656565656565656"
        - result [string]: result after the analysis of the PixChargeback by the receiving party. Options: "rejected", "accepted", "partiallyAccepted"
        - status [string]: current PixChargeback status. Options: "created", "failed", "delivered", "closed", "canceled".
        - created [DateTime, default null]: created datetime for the PixChargeback. ex: "2020-03-10 10:30:00.000"
        - updated [DateTime, default null]: update datetime for the PixChargeback. ex: "2020-03-10 10:30:00.000"
         */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> amount = Checks::checkParam($params, "amount");
        $this-> referenceId = Checks::checkParam($params, "referenceId");
        $this-> reason = Checks::checkParam($params, "reason");
        $this-> description = Checks::checkParam($params, "description");
        $this-> analysis = Checks::checkParam($params, "analysis");
        $this-> bacenId = Checks::checkParam($params, "bacenId");
        $this-> sendeBankCode = Checks::checkParam($params, "sendeBankCode");
        $this-> receiverBankCode = Checks::checkParam($params, "receiverBankCode");
        $this-> rejectionReason = Checks::checkParam($params, "rejectionReason");
        $this-> chargebackId = Checks::checkParam($params, "chargebackId");
        $this-> id = Checks::checkParam($params, "id");
        $this-> result = Checks::checkParam($params, "result");
        $this-> status = Checks::checkParam($params, "status");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));
        
        Checks::checkParams($params);
    }

    /**
    # Create a PixChargeback object

    Create a PixChargeback in the Stark Infra API

    ## Parameters (optional):
        - chargeback [PixChargeback object]: PixChargeback object to be created in the API.
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixChargeback object with updated attributes.
     */
    public static function create($chargeback, $user = null)
    {
        return Rest::postSingle($user, PixChargeback::resource(), $chargeback);
    }

    /**
    # Retrieve a PixChargeback object

    Retrieve the PixChargeback object linked to your Workspace in the Stark Infra API using its id.
    
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
    # Retrieve PixChargeback

    Receive a generator of PixChargeback objects previously created in the Stark Infra API
    
        ## Parameters (optional):
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [list of strings, default null]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "closed", "canceled".
        - ids [list of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call

    ## Return:
        - generator of PixChargeback objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, PixChargeback::resource(), $options);
    }

    /**
    # Retrieve PixChargeback
    
    Receive a generator of PixChargeback objects previously created in the Stark Infra API
    
    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [list of strings, default null]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "closed", "canceled".
        - ids [list of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [list of strings, default null]: filter for the type of retrieved PixChargeback. Options: "fraud", "reversal", "reversalChargeback"
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - cursor to retrieve the next page of PixChargeback objects
        - generator of PixChargeback objects with updated attributes
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
        - rejectionReason [string, default null]: if the PixChargeback is rejected a reason is required. Options: "noBalance", "accountClosed", "unableToReverse",
        - chargebackId [string, default null]: returnId of the chargeback transaction. ex: "D20018183202201201450u34sDGd19lz"
    
    ## Parameters (optional):
        - analysis [string, default null]: description of the analysis that led to the result.
    
    ## Return:
        - PixChargeback with updated attributes
    */
    public static function update($id, $options = [], $user=null)
    {
        return Rest::patchId($user, PixChargeback::resource(), $id, $options);
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
