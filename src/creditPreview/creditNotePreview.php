<?php

namespace StarkInfra\CreditPreview;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\SubResource;
use StarkInfra\CreditNote\Invoice;


class CreditNotePreview extends SubResource
{
    /**
    # CreditNotePreview object

    A CreditNotePreview is used to preview a CCB contract between the borrower 
    and lender with a specific table type. When you initialize a CreditNotePreview, 
    the entity will be automatically sent to the Stark Infra API.
    The 'create' function sends the objects to the Stark Infra API and 
    returns the list of preview data.

    ## Parameters (required):
        - type [string]: table type that defines the amortization system. Options: "sac", "price", "american", "bullet", "custom"
        - nominalAmount [integer]: amount in cents transferred to the credit receiver, before deductions. ex: nominalAmount=11234 (= R$ 112.34)
        - scheduled [Date, DateTime or string ]: date of payment execution to the credit receiver. ex: "2023-10-25T17:59:26.249976+00:00"
        - taxId [string]: credit receiver's tax ID (CPF or CNPJ). ex: "20.018.183/0001-80"

    ## Parameters (conditionally required):
        - invoices [list of CreditNote.Invoice objects]: list of Invoice objects to be created and sent to the credit receiver.
        - nominalInterest [float]: yearly nominal interest rate of the credit note, in percentage. ex: 12.5
        - initialDue [Date, DateTime or string]: date of the first Invoice. ex: "2023-11-25T17:59:26.249976+00:00"
        - count [integer]: quantity of Invoices for payment. ex: 12
        - initialAmount [integer]: value of the first Invoice in cents. ex: 1234 (= R$12.34)
        - interval [string]: interval between Invoices. ex: "year", "month"

    ## Parameters (optional):
        - rebateAmount [integer, default 0]: credit analysis fee deducted from lent amount. ex: rebateAmount=11234 (= R$ 112.34)

    ## Attributes (return-only):
        - amount [integer]: CreditNote value in cents. ex: 1234 (= R$ 12.34)
        - interest [float]: yearly effective interest rate of the CreditNote, in percentage. ex: 12.5
        - taxAmount [integer]: tax amount included in the CreditNote. ex: 100
     */
    function __construct(array $params)
    {
        $this-> type = Checks::checkParam($params, "type");
        $this-> nominalAmount = Checks::checkParam($params, "nominalAmount");
        $this-> scheduled = Checks::checkDateTime(Checks::checkParam($params, "scheduled"));
        $this-> taxId = Checks::checkParam($params, "taxId");
        $this-> invoices = Invoice::parseInvoices(Checks::checkParam($params, "invoices"));
        $this-> nominalInterest = Checks::checkParam($params, "nominalInterest");
        $this-> initialDue = Checks::checkDateTime(Checks::checkParam($params, "initialDue"));
        $this-> count = Checks::checkParam($params, "count");
        $this-> rebateAmount = Checks::checkParam($params, "rebateAmount");
        $this-> amount = Checks::checkParam($params, "amount");
        $this-> initialAmount = Checks::checkParam($params, "initialAmount");
        $this-> interest = Checks::checkParam($params, "interest");
        $this-> taxAmount = Checks::checkParam($params, "taxAmount");
        $this-> interval = Checks::checkParam($params, "interval");

        Checks::checkParams($params);
    }
}
