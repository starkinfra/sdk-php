<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class IssuingBillingTransaction extends Resource
{

    public $amount;
    public $invoiceId;
    public $installment;
    public $installmentCount;
    public $balance;
    public $holderName;
    public $source;
    public $externalId;
    public $description;
    public $cardEnding;
    public $tax;
    public $rate;
    public $merchantAmount;
    public $merchantCurrencyCode;
    public $created;

    /**
    # IssuingBillingTransaction object

    The IssuingBillingTransaction objects created in your Workspace track the
    movements that compose your issuing billing invoices.

    ## Attributes (return-only):
        -id [string]: unique id returned when the IssuingBillingTransaction is created. ex: "5656565656565656"
        -amount [integer]: transaction amount in cents. ex: 11234 (= R$ 112.34)
        -invoiceId [string]: parent billing invoice id. May be null. ex: "5656565656565656"
        -installment [integer]: installment number of the transaction. ex: 1
        -installmentCount [integer]: total installment count of the transaction. ex: 12
        -balance [integer]: remaining balance in cents. ex: 11234 (= R$ 112.34)
        -holderName [string]: card holder name. ex: "Tony Stark"
        -source [string]: transaction source. ex: "issuing"
        -externalId [string]: external transaction id. ex: "my-external-id-123456"
        -description [string]: transaction description. ex: "Payment for service #1234"
        -cardEnding [string]: last 4 digits of the card. ex: "1234"
        -tax [integer]: IOF amount in cents applied to the transaction
        -rate [float]: Conversion rate applied to international transactions
        -merchantAmount [integer]: merchant amount in cents. ex: 11234 (= R$ 112.34)
        -merchantCurrencyCode [string]: merchant currency code (ISO 4217). ex: "USD"
        -created [DateTime]: creation datetime for the IssuingBillingTransaction.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->amount = Checks::checkParam($params, "amount");
        $this->invoiceId = Checks::checkParam($params, "invoiceId");
        $this->installment = Checks::checkParam($params, "installment");
        $this->installmentCount = Checks::checkParam($params, "installmentCount");
        $this->balance = Checks::checkParam($params, "balance");
        $this->holderName = Checks::checkParam($params, "holderName");
        $this->source = Checks::checkParam($params, "source");
        $this->externalId = Checks::checkParam($params, "externalId");
        $this->description = Checks::checkParam($params, "description");
        $this->cardEnding = Checks::checkParam($params, "cardEnding");
        $this->tax = Checks::checkParam($params, "tax");
        $this->rate = Checks::checkParam($params, "rate");
        $this->merchantAmount = Checks::checkParam($params, "merchantAmount");
        $this->merchantCurrencyCode = Checks::checkParam($params, "merchantCurrencyCode");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));

        Checks::checkParams($params);
    }

    /**
    # Retrieve IssuingBillingTransactions

    Receive an enumerator of IssuingBillingTransaction objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null]: date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created only before specified date. ex: "2020-04-03"
        - invoiceId [string, default null]: filter for transactions of a specific billing invoice. ex: "5656565656565656"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingBillingTransaction objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, IssuingBillingTransaction::resource(), $options);
    }

    /**
    # Retrieve paged IssuingBillingTransactions

    Receive a list of up to 100 IssuingBillingTransaction objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null]: date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created only before specified date. ex: "2020-04-03"
        - invoiceId [string, default null]: filter for transactions of a specific billing invoice. ex: "5656565656565656"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of IssuingBillingTransaction objects with updated attributes
        - cursor to retrieve the next page of IssuingBillingTransaction objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, IssuingBillingTransaction::resource(), $options);
    }

    private static function resource()
    {
        $transaction = function ($array) {
            return new IssuingBillingTransaction($array);
        };
        return [
            "name" => "IssuingBillingTransaction",
            "maker" => $transaction,
        ];
    }
}
