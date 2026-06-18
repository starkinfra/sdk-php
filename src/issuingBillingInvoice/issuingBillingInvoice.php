<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class IssuingBillingInvoice extends Resource
{

    public $taxId;
    public $name;
    public $fine;
    public $interest;
    public $status;
    public $amount;
    public $nominalAmount;
    public $brcode;
    public $link;
    public $due;
    public $start;
    public $end;
    public $created;
    public $updated;

    /**
    # IssuingBillingInvoice object

    The IssuingBillingInvoice objects created in your Workspace are the Pix invoices
    used to charge for your issuing operation.

    ## Attributes (return-only):
        -id [string]: unique id returned when the IssuingBillingInvoice is created. ex: "5656565656565656"
        -taxId [string]: payer tax ID (CPF or CNPJ). ex: "012.345.678-90"
        -name [string]: payer name. ex: "Tony Stark"
        -fine [float]: fine amount charged on the invoice in cents. ex: 200 (= R$ 2.00)
        -interest [float]: interest amount charged on the invoice in cents. ex: 100 (= R$ 1.00)
        -status [string]: current IssuingBillingInvoice status. ex: "paid" or "expired"
        -amount [integer]: invoice amount in cents. ex: 11234 (= R$ 112.34)
        -nominalAmount [integer]: nominal invoice amount in cents. ex: 11234 (= R$ 112.34)
        -brcode [string]: BR Code for the invoice payment. ex: "00020101021226930014br.gov.bcb.pix..."
        -link [string]: public invoice webpage URL. ex: "https://starkbank-card-issuer.sandbox.starkbank.com/billinginvoicelink/97de4d51e8984c459639a645ce920abb"
        -due [DateTime]: invoice due datetime.
        -start [DateTime]: billing cycle start datetime.
        -end [DateTime]: billing cycle end datetime.
        -created [DateTime]: creation datetime for the IssuingBillingInvoice.
        -updated [DateTime]: latest update datetime for the IssuingBillingInvoice.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->taxId = Checks::checkParam($params, "taxId");
        $this->name = Checks::checkParam($params, "name");
        $this->fine = Checks::checkParam($params, "fine");
        $this->interest = Checks::checkParam($params, "interest");
        $this->status = Checks::checkParam($params, "status");
        $this->amount = Checks::checkParam($params, "amount");
        $this->nominalAmount = Checks::checkParam($params, "nominalAmount");
        $this->brcode = Checks::checkParam($params, "brcode");
        $this->link = Checks::checkParam($params, "link");
        $this->due = Checks::checkDateTime(Checks::checkParam($params, "due"));
        $this->start = Checks::checkDateTime(Checks::checkParam($params, "start"));
        $this->end = Checks::checkDateTime(Checks::checkParam($params, "end"));
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Retrieve a specific IssuingBillingInvoice

    Receive a single IssuingBillingInvoice object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingBillingInvoice object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IssuingBillingInvoice::resource(), $id);
    }

    /**
    # Retrieve IssuingBillingInvoices

    Receive an enumerator of IssuingBillingInvoice objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null]: date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. ex: ["paid", "expired"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingBillingInvoice objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, IssuingBillingInvoice::resource(), $options);
    }

    /**
    # Retrieve paged IssuingBillingInvoices

    Receive a list of up to 100 IssuingBillingInvoice objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null]: date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. ex: ["paid", "expired"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of IssuingBillingInvoice objects with updated attributes
        - cursor to retrieve the next page of IssuingBillingInvoice objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, IssuingBillingInvoice::resource(), $options);
    }

    private static function resource()
    {
        $invoice = function ($array) {
            return new IssuingBillingInvoice($array);
        };
        return [
            "name" => "IssuingBillingInvoice",
            "maker" => $invoice,
        ];
    }
}
