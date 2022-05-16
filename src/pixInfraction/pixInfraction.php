<?php

namespace StarkInfra;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\StarkDate;

class PixInfraction extends Resource
{
    /**
    # PixInfraction object

    Pix Infractions are used to report transactions that are suspected of
    fraud, to request a refund or to reverse a refund.
    When you initialize a PixInfraction, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the created object.
    
    ## Parameters (required):
        - referenceId [string]: endToEndId or returnId of the transaction being reported. ex: "E20018183202201201450u34sDGd19lz"
        - type [string]: type of Pix infraction. Options: "fraud", "reversal", "reversalChargeback"
    
    ## Attributes (return-only):
        - description [string, Default null]: description for any details that can help with the infraction investigation.
        - creditedBankCode [string, Default null]: bankCode of the credited Pix participant in the reported transaction. ex: "20018183"
        - agent [string, Default null]: Options: "reporter" if you created the PixInfraction, "reported" if you received the PixInfraction.
        - analysis [string, Default null]: analysis that led to the result.
        - bacenId [string, Default null]: central bank's unique UUID that identifies the Pix Infraction.
        - debitedBankCode [string, Default null]: bankCode of the debited Pix participant in the reported transaction. ex: "20018183"
        - id [string, default null]: unique id returned when the PixInfraction is created. ex: "5656565656565656"
        - reportedBy [string, Default null]: agent that reported the PixInfraction. Options: "debited", "credited".
        - result [string, Default null]: result after the analysis of the PixInfraction by the receiving party. Options: "agreed", "disagreed"
        - status [string, default null]: current PixInfraction status. Options: "created", "failed", "delivered", "closed", "canceled".
        - created [DateTime, default null]: created datetime for the PixInfraction. ex: "2020-03-10 10:30:00.000"
        - updated [DateTime, default null]: update datetime for the PixInfraction. ex: "2020-03-10 10:30:00.000"
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> referenceId = Checks::checkParam($params, "referenceId");
        $this-> type = Checks::checkParam($params, "type");
        $this-> description = Checks::checkParam($params, "description");
        $this-> creditedBankCode = Checks::checkParam($params, "creditedBankCode");
        $this-> agent = Checks::checkParam($params, "agent");
        $this-> analysis = Checks::checkParam($params, "analysis");
        $this-> bacenId = Checks::checkParam($params, "bacenId");
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

    Create a PixInfraction in the Stark Infra API
    
    ## Parameters (optional):
        - infraction [PixInfraction object]: PixInfraction object to be created in the API.
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixInfraction object with updated attributes.
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
    
    Receive a generator of PixInfraction objects previously created in the Stark Infra API
    
    ## Parameters (optional):
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [list of strings, default null]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "closed", "canceled".
        - ids [list of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [list of strings, default null]: filter for the type of retrieved PixInfraction. Options: "fraud", "reversal", "reversalChargeback"
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - generator of PixInfraction objects with updated attributes
    */
    public static function query($options=[], $user=null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        $options["status"] = Checks::checkParam($options, "status");
        $options["ids"] = Checks::checkParam($options, "ids");
        $options["type"] = Checks::checkParam($options, "type");

        return Rest::getList($user, PixInfraction::resource(), $options);
    }

    /** 
    # Retrieve paged PixInfractions

    Receive a list of up to 100 PixInfraction objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.
    
    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [list of strings, default null]: filter for status of retrieved objects. Options: "created", "failed", "delivered", "closed", "canceled".
        - ids [list of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - type [list of strings, default null]: filter for the type of retrieved PixInfraction. Options: "fraud", "reversal", "reversalChargeback"
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - list of PixInfraction objects with updated attributes and cursor to retrieve the next page of PixInfraction objects
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
    
    ## Parameters (optional):
        - analysis [string, default null]: analysis that led to the result.
    
    ## Return:
        - PixInfraction with updated attributes
    */
    public static function update($id, $options = [], $user=null)
    {
        return Rest::patchId($user, PixInfraction::resource(), $id, $options);
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
