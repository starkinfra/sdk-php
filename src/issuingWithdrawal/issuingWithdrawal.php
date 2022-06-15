<?php

namespace StarkInfra;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\StarkDate;


class IssuingWithdrawal extends Resource
{
    /**
    # IssuingWithdrawal object

    Displays the IssuingWithdrawal objects created to your Workspace.

    ## Parameters (required):
        - amount [integer]: IssuingWithdrawal value in cents. Minimum = 0 (any value will be accepted). ex: 1234 (= R$ 12.34)
        - externalId [string] IssuingWithdrawal external ID. ex: "12345"
        - description [string]: IssuingWithdrawal description. ex: "sending money back"

    ## Parameters (optional):
        - tags [array of strings, default null]: array of strings for tagging

    ## Attributes (return-only):
        - id [string]: unique id returned when IssuingWithdrawal is created. ex: "5656565656565656"
        - transactionId [string]: Stark Bank ledger transaction ids linked to this IssuingWithdrawal
        - issuingTransactionId [string]: ledger transaction ids linked to this IssuingWithdrawal. ex: "issuing-withdrawal/5656565656565656"
        - created [DateTime]: creation datetime for the IssuingWithdrawal. 
        - updated [DateTime]: latest update datetime for the IssuingWithdrawal. 
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->amount = Checks::checkParam($params, "amount");
        $this->externalId = Checks::checkParam($params, "externalId");
        $this->description = Checks::checkParam($params, "description");
        $this->tags = Checks::checkParam($params, "tags");
        $this->transactionId = Checks::checkParam($params, "transactionId");
        $this->issuingTransactionId = Checks::checkParam($params, "issuingTransactionId");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create an IssuingWithdrawal

    Send an IssuingWithdrawal object for creation in the Stark Infra API

    ## Parameters (required):
        - withdrawal [IssuingWithdrawal object]: list of IssuingWithdrawal objects to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingWithdrawal object with updated attributes
     */
    public static function create($withdrawal, $user = null)
    {
        return Rest::postSingle($user, IssuingWithdrawal::resource(), $withdrawal);
    }

    /**
    # Retrieve a specific IssuingWithdrawal

    Receive a single IssuingWithdrawal object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingWithdrawal object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IssuingWithdrawal::resource(), $id);
    }

    /**
    # Retrieve IssuingWithdrawal

    Receive an enumerator of IssuingWithdrawal objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - externalIds [array of strings, default []]: external IDs. ex: ["5656565656565656", "4545454545454545"]
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingWithdrawals objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, IssuingWithdrawal::resource(), $options);
    }

    /**
    # Retrieve IssuingWithdrawal

    Receive a list of IssuingWithdrawal objects previously created in the Stark Infra API and the cursor to the next page.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - externalIds [array of strings, default []]: external IDs. ex: ["5656565656565656", "4545454545454545"]
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - list of IssuingWithdrawal objects with updated attributes
        - cursor to retrieve the next page of IssuingWithdrawal objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, IssuingWithdrawal::resource(), $options);
    }

    private static function resource()
    {
        $withdrawal = function ($array) {
            return new IssuingWithdrawal($array);
        };
        return [
            "name" => "IssuingWithdrawal",
            "maker" => $withdrawal,
        ];
    }
}
