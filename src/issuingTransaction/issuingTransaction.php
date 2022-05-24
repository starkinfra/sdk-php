<?php

namespace StarkInfra;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\StarkDate;


class IssuingTransaction extends Resource
{
    /**
    # IssuingTransaction object

    Displays the IssuingTransaction objects created to your Workspace.

    ## Attributes (return-only):
        - id [string, default null]: unique id returned when IssuingTransaction is created. ex: "5656565656565656"
        - amount [integer, default null]: IssuingTransaction value in cents. Minimum = 0 (any value will be accepted). ex: 1234 (= R$ 12.34)
        - subIssuerId [string, default null]:
        - balance [integer, default null]: balance amount of the workspace at the instant of the Transaction in cents. ex: 200 (= R$ 2.00)
        - description [string, default null]: IssuingTransaction description. ex: "Buying food"
        - source [string, default null]: source of the transaction. ex: "issuing-purchase/5656565656565656"
        - tags [string, default null]: list of strings for tagging ex: ["tony", "stark"]
        - created [string, default null]: creation datetime for the IssuingTransaction. ex: "2020-03-10 10:30:00.000"
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->amount = Checks::checkParam($params, "amount");
        $this->subIssuerId = Checks::checkParam($params, "subIssuerId");
        $this->balance = Checks::checkParam($params, "balance");
        $this->description = Checks::checkParam($params, "description");
        $this->source = Checks::checkParam($params, "source");
        $this->tags = Checks::checkParam($params, "tags");
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific IssuingTransaction

    Receive a single IssuingTransaction object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingTransaction object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IssuingTransaction::resource(), $id);
    }

    /**
    # Retrieve IssuingTransaction

    Receive an enumerator of IssuingTransaction objects previously created in the Stark Infra API

    ## Parameters (optional):
        - tags [list of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - externalIds [list of strings, default []]: external IDs. ex: ["5656565656565656", "4545454545454545"]
        - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "approved", "canceled", "denied", "confirmed" or "voided"
        - ids [list of strings, default [], default null]: purchase IDs
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of Transaction objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, IssuingTransaction::resource(), $options);
    }

    /**
    # Retrieve paged IssuingTransaction

    Receive a list of up to 100 IssuingTransaction objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - tags [list of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - externalIds [list of strings, default []]: external IDs. ex: ["5656565656565656", "4545454545454545"]
        - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "approved", "canceled", "denied", "confirmed" or "voided"
        - ids [list of strings, default [], default null]: purchase IDs
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - list of IssuingTransaction objects with updated attributes
        - cursor to retrieve the next page of IssuingTransaction objects
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, IssuingTransaction::resource(), $options);
    }

    private static function resource()
    {
        $transaction = function ($array) {
            return new IssuingTransaction($array);
        };
        return [
            "name" => "IssuingTransaction",
            "maker" => $transaction,
        ];
    }
}
