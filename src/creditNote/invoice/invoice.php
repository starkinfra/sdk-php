<?php

namespace StarkInfra\CreditNote;
use StarkInfra\Utils\API;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;


class Invoice extends Resource
{
    /**
    # CreditNote\Invoice object
    Invoice issued after the contract is signed, to be paid by the credit receiver.
    
    ## Parameters (required):
        - amount [integer]: Invoice value in cents. Minimum = 1 (any value will be accepted). ex: 1234 (= R$ 12.34)
    
    ## Parameters (optional):
        - due [DateTime or Date or string, default now + 2 days]: Invoice due date in UTC ISO format. ex: "2020-03-10 10:30:00.000" for immediate invoices and "2020-10-28" for scheduled invoices
        - expiration [integer or DateTime, default 5097600 (59 days)]: time interval in seconds between due date and expiration date. ex 123456789
        - tags [array of strings, default null]: list of strings for tagging
        - descriptions [array of Invoice\Description objects, default null]: list of Invoice\Description objects with "key":string and (optional) "value":string pairs
        
        ## Attributes (return-only):
        - name [string]: payer name. ex: "Iron Bank S.A."
        - taxId [string]: payer tax ID (CPF or CNPJ) with or without formatting. ex: "01234567890" or "20.018.183/0001-80"
        - pdf [string]: public Invoice PDF URL. ex: "https://invoice.starkbank.com/pdf/d454fa4e524441c1b0c1a729457ed9d8"
        - fine [float]: Invoice fine for overdue payment in %. ex: 2.5
        - interest [float]: Invoice monthly interest for overdue payment in %. ex: 5.2
        - link [string]: public Invoice webpage URL. ex: "https://my-workspace.sandbox.starkbank.com/invoicelink/d454fa4e524441c1b0c1a729457ed9d8"
        - nominalAmount [integer]: Invoice emission value in cents (will change if invoice is updated, but not if it's paid). ex: 400000
        - fineAmount [integer]: Invoice fine value calculated over nominalAmount. ex: 20000
        - interestAmount [integer]: Invoice interest value calculated over nominalAmount. ex: 10000
        - discountAmount [integer]: Invoice discount value calculated over nominalAmount. ex: 3000
        - discounts [array of Invoice\Discount objects]: list of Invoice\Discount objects with "percentage":float and "due":DateTime or string pairs
        - id [string]: unique id returned when Invoice is created. ex: "5656565656565656"
        - brcode [string]: BR Code for the Invoice payment. ex: "00020101021226800014br.gov.bcb.pix2558invoice.starkbank.com/f5333103-3279-4db2-8389-5efe335ba93d5204000053039865802BR5913Arya Stark6009Sao Paulo6220051656565656565656566304A9A0"
        - status [string]: current Invoice status. ex: "registered" or "paid"
        - fee [integer]: fee charged by this Invoice. ex: 200 (= R$ 2.00)
        - transactionIds [array of strings]: ledger transaction ids linked to this Invoice (if there are more than one, all but the first are reversals or failed reversal chargebacks). ex: ["19827356981273"]
        - created [DateTime]: creation datetime for the Invoice. 
        - updated [DateTime]: latest update datetime for the Invoice. 
    */

    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> amount = Checks::checkParam($params, "amount");
        $this-> due = Checks::checkDateTime(Checks::checkParam($params, "due"));
        $this-> expiration = Checks::checkDateInterval(Checks::checkParam($params, "expiration"));
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> descriptions = Invoice::parseDescriptions(Checks::checkParam($params, "descriptions"));
        $this-> name = Checks::checkParam($params, "name");
        $this-> taxId = Checks::checkParam($params, "taxId");
        $this-> pdf = Checks::checkParam($params, "pdf");
        $this-> fine = Checks::checkParam($params, "fine");
        $this-> interest = Checks::checkParam($params, "interest");
        $this-> link = Checks::checkParam($params, "link");
        $this-> nominalAmount = Checks::checkParam($params, "nominalAmount");
        $this-> fineAmount = Checks::checkParam($params, "fineAmount");
        $this-> interestAmount = Checks::checkParam($params, "interestAmount");
        $this-> discountAmount = Checks::checkParam($params, "discountAmount");
        $this-> discounts = Invoice::parseDiscounts(Checks::checkParam($params, "discounts"));
        $this-> brcode = Checks::checkParam($params, "brcode");
        $this-> status = Checks::checkParam($params, "status");
        $this-> fee = Checks::checkParam($params, "fee");
        $this-> transactionIds = Checks::checkParam($params, "transactionIds");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));
    }

    public static function parseInvoices($invoices) {
        if (is_null($invoices)){
            return null;
        }
        $parsedInvoices = [];
        foreach($invoices as $invoice) {
            if($invoice instanceof Invoice) {
                array_push($parsedInvoices, $invoice);
                continue;
            }
            $parsedInvoice = function ($array) {
                $invoiceMaker = function ($array) {
                    return new Invoice($array);
                };
                return API::fromApiJson($invoiceMaker, $array);
            };
            array_push($parsedInvoices, $parsedInvoice($invoice));
        }    
        return $parsedInvoices;
    }

    public static function parseDescriptions($descriptions) {
        if ($descriptions == null) {
            return null;
        }
        $parsedDescriptions = [];
        foreach($descriptions as $description) {
            if($description instanceof Invoice\Description) {
                array_push($parsedDescriptions, $description);
                continue;
            }
            $parsedDescription = function ($array) {
                $descriptionMaker = function ($array) {
                    return new Invoice\Description($array);
                };
                return API::fromApiJson($descriptionMaker, $array);
            };
            array_push($parsedDescriptions, $parsedDescription($description));
        }    
        return $parsedDescriptions;
    }

    public static function parseDiscounts($discounts) {
        if ($discounts == null) {
            return null;
        }
        $parsedDiscounts = [];
        foreach($discounts as $discount) {
            if($discount instanceof Invoice\Discount) {
                array_push($parsedDiscounts, $discount);
                continue;
            }
            $parsedDiscount = function ($array) {
                $discountMaker = function ($array) {
                    return new Invoice\Discount($array);
                };
                return API::fromApiJson($discountMaker, $array);
            };
            array_push($parsedDiscounts, $parsedDiscount($discount));
        }    
        return $parsedDiscounts;
    }

    public static function resource()
    {
        $invoice = function ($array) {
            return new Invoice($array);
        };
        return [
            "name" => "Invoice",
            "maker" => $invoice,
        ];
    }
}
