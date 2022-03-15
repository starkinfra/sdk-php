<?php

namespace StarkInfra;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\StarkDate;


class IssuingInvoice extends Resource
{
    /**
    # IssuingInvoice object

    Displays the IssuingInvoice objects created to your Workspace.

    ## Parameters (required):
        - amount [integer]: IssuingInvoice value in cents. Minimum = 0 (R$0,00). ex: 1234 (= R$ 12.34)

    ## Parameters (optional):
        - taxId [string]: payer tax ID (CPF or CNPJ) with or without formatting. ex: "01234567890" or "20.018.183/0001-80"
        - name [string]: payer name. ex: "Iron Bank S.A."
        - tags [array of strings, default null]: array of strings for tagging

    ## Attributes (return-only):
        - id [string, default null]: unique id returned when IssuingInvoice is created. ex: "5656565656565656"
        - status [string, default null]: current IssuingInvoice status. ex: "created", "paid", "canceled" or "overdue"
        - issuingTransactionId [string, default null]: ledger transaction ids linked to this IssuingInvoice. ex: "issuing-invoice/5656565656565656"
        - created [string, default null]: creation datetime for the IssuingInvoice. ex: "2020-03-10 10:30:00.000"
        - updated [string, default null]: latest update datetime for the IssuingInvoice. ex: "2020-03-10 10:30:00.000"
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->amount = Checks::checkParam($params, "amount");
        $this->taxId = Checks::checkParam($params, "taxId");
        $this->name = Checks::checkParam($params, "name");
        $this->tags = Checks::checkParam($params, "tags");
        $this->status = Checks::checkParam($params, "status");
        $this->issuingTransactionId = Checks::checkParam($params, "issuingTransactionId");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create Invoices

    Send a list of IssuingInvoice objects for creation in the Stark Infra API

    ## Parameters (required):
        - invoices [array of IssuingInvoice objects]: array of IssuingInvoice objects to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of IssuingInvoice objects with updated attributes
     */
    public static function create($invoices, $user = null)
    {
        return Rest::postSingle($user, IssuingInvoice::resource(), $invoices);
    }

    /**
    # Retrieve a specific IssuingInvoice

    Receive a single IssuingInvoice object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IssuingInvoice object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IssuingInvoice::resource(), $id);
    }

    /**
    # Retrieve IssuingInvoices

    Receive an generator of IssuingInvoice objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [DateTime or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [DateTime or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "created", "paid", "canceled" or "overdue"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - fields [list of string, default []]: fields to be returned. ex: ["id", "amount", "name"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - generator of IssuingInvoices objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, IssuingInvoice::resource(), $options);
    }

    /**
    # Retrieve paged Invoices

    Receive a list of up to 100 Invoice objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default None]: maximum number of objects to be retrieved. Unlimited if None. ex: 35
        - after [datetime.date or string, default None] date filter for objects created only after specified date. ex: datetime.date(2020, 3, 10)
        - before [datetime.date or string, default None] date filter for objects created only before specified date. ex: datetime.date(2020, 3, 10)
        - status [string, default None]: filter for status of retrieved objects. ex: "paid" or "registered"
        - tags [list of strings, default None]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - fields [list of string, default None]: fields to be returned. ex: ["id", "amount", "name"]
        - user [Organization/Project object, default None]: Organization or Project object. Not necessary if starkinfra.user was set before function call
    
    ## Return:
        - list of IssuingInvoices objects with updated attributes
        - cursor to retrieve the next page of IssuingInvoices objects
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, IssuingInvoice::resource(), $options);
    }

    private static function resource()
    {
        $invoice = function ($array) {
            return new IssuingInvoice($array);
        };
        return [
            "name" => "IssuingInvoice",
            "maker" => $invoice,
        ];
    }
}
