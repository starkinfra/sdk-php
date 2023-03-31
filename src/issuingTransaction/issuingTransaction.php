<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class IssuingTransaction extends Resource
{

    public $amount;
    public $balance;
    public $description;
    public $source;
    public $tags;
    public $created;

    /**
    # IssuingTransaction object

    The IssuingTransaction objects created in your Workspace to represent each balance shift.

    ## Attributes (return-only):
        - id [string]: unique id returned when IssuingTransaction is created. ex: "5656565656565656"
        - amount [integer]: IssuingTransaction value in cents. ex: 1234 (= R$ 12.34)
        - balance [integer]: balance amount of the Workspace at the instant of the Transaction in cents. ex: 200 (= R$ 2.00)
        - description [string]: IssuingTransaction description. ex: "Buying food"
        - source [string]: source of the transaction. ex: "issuing-purchase/5656565656565656"
        - tags [string]: list of strings inherited from the source resource. ex: ["tony", "stark"]
        - created [string]: creation datetime for the IssuingTransaction. 
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->amount = Checks::checkParam($params, "amount");
        $this->balance = Checks::checkParam($params, "balance");
        $this->description = Checks::checkParam($params, "description");
        $this->source = Checks::checkParam($params, "source");
        $this->tags = Checks::checkParam($params, "tags");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));

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
    # Retrieve IssuingTransactions

    Receive an enumerator of IssuingTransaction objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - externalIds [array of strings, default []]: external IDs. ex: ["5656565656565656", "4545454545454545"]
        - status [string, default null]: filter for status of retrieved objects. ex: "approved", "canceled", "denied", "confirmed" or "voided"
        - ids [array of strings, default [], default null]: purchase IDs
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
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - externalIds [array of strings, default []]: external IDs. ex: ["5656565656565656", "4545454545454545"]
        - status [string, default null]: filter for status of retrieved objects. ex: "approved", "canceled", "denied", "confirmed" or "voided"
        - ids [array of strings, default []]: purchase IDs
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - list of IssuingTransaction objects with updated attributes
        - cursor to retrieve the next page of IssuingTransaction objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
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
