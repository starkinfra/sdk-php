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
        - id [string]: unique id returned when IssuingInvoice is created. ex: "5656565656565656"
        - status [string]: current IssuingInvoice status. ex: "created", "paid", "canceled" or "overdue"
        - issuingTransactionId [string]: ledger transaction ids linked to this IssuingInvoice. ex: "issuing-invoice/5656565656565656"
        - created [DateTime]: creation datetime for the IssuingInvoice. 
        - updated [DateTime]: latest update datetime for the IssuingInvoice. 
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

    Receive an enumerator of IssuingInvoice objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "created", "paid", "canceled" or "overdue"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - fields [list of string, default []]: fields to be returned. ex: ["id", "amount", "name"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IssuingInvoices objects with updated attributes
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
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "paid" or "registered"
        - tags [list of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - fields [list of string, default null]: fields to be returned. ex: ["id", "amount", "name"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - list of IssuingInvoices objects with updated attributes
        - cursor to retrieve the next page of IssuingInvoices objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
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
