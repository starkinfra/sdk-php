<?php

namespace StarkInfra;
use StarkInfra\Utils\API;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\StarkDate;
use StarkInfra\CreditNote\Invoice;


class CreditNote extends Resource
{
    /**
    # CreditNote object

    CreditNotes are used to generate CCB contracts between you and your customers.
    When you initialize a CreditNote, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the list of created objects.

    ## Parameters (required):
        - templateId [string]: ID of the contract template on which the credit note will be based. ex: templateId="0123456789101112"
        - name [string]: credit receiver's full name. ex: name="Edward Stark"
        - taxId [string]: credit receiver's tax ID (CPF or CNPJ). ex: "20.018.183/0001-80"
        - nominalAmount [integer]: amount in cents transferred to the credit receiver, before deductions. ex: nominalAmount=11234 (= R$ 112.34)
        - scheduled [Date or string]: date of transfer execution. ex: "2020-03-10"
        - invoices [array of Invoice objects or dictionaries]: list of Invoices to be created and sent to the credit receiver. ex: invoices=[Invoice(), Invoice()]
        - payment [Transfer object or dictionary]: payment to be created and sent to the credit receiver. ex: payment=CreditNote\Transfer()
        - signers [array of Signer objects or dictionaries]: Signers contain the name and email of the signer and the method of delivery. ex: signers=[{"name": "Tony Stark", "contact": "tony@starkindustries.com", "method": "link"}]
        - externalId [string]: url safe string that must be unique among all your CreditNotes. ex: externalId="my-internal-id-123456"
        - streetLine1 [string]: credit receiver main address. ex: "Av. Paulista, 200"
        - streetLine2 [string]: credit receiver address complement. ex: "Apto. 123"
        - district [string]: credit receiver address district / neighbourhood. ex: "Bela Vista"
        - city [string]: credit receiver address city. ex: "Rio de Janeiro"
        - stateCode [string]: credit receiver address state. ex: "GO"
        - zipCode [string]: credit receiver address zip code. ex: "01311-200"

    ## Parameters (conditionally required):
        - paymentType [string]: payment type, inferred from the payment parameter if it is not a dictionary. ex: "transfer"
        
    ## Parameters (optional):
        - rebateAmount [integer, default 0]: credit analysis fee deducted from lent amount. ex: rebateAmount=11234 (= R$ 112.34)
        - tags [array of strings, default []]: list of strings for reference when searching for CreditNotes. ex: tags=["employees", "monthly"]
        - expiration [DateTinterval or integer, default 604800 (7 days)]: time interval in seconds between scheduled date and expiration date.

    ## Attributes (return-only):
        - id [string]: unique id returned when the CreditNote is created. ex: "5656565656565656"
        - amount [integer]: CreditNote value in cents. ex: 1234 (= R$ 12.34)
        - documentId [string]: ID of the signed document to execute this CreditNote. ex: "4545454545454545"
        - status [string]: current status of the CreditNote. ex: "created"
        - transactionIds [array of strings]: ledger transaction ids linked to this CreditNote. ex: ["19827356981273"]
        - workspaceId [string]: ID of the Workspace that generated this CreditNote. ex: "4545454545454545"
        - taxAmount [float]: tax amount included in the CreditNote. ex: 100
        - interest [float]: yearly effective interest rate of the credit note, in percentage. ex: 12.5
        - nominalInterest [float]: yearly nominal interest rate of the creditote, in percentage. ex: 11.5
        - created [DateTime]: creation datetime for the CreditNote.
        - updated [DateTime]: latest update datetime for the CreditNote.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> templateId = Checks::checkParam($params, "templateId");
        $this-> name = Checks::checkParam($params, "name");
        $this-> taxId = Checks::checkParam($params, "taxId");
        $this-> nominalAmount = Checks::checkParam($params, "nominalAmount");
        $this-> scheduled = Checks::checkDateTime(Checks::checkParam($params, "scheduled"));
        $this-> invoices = Invoice::parseInvoices(Checks::checkParam($params, "invoices"));
        $this-> payment = Checks::checkParam($params, "payment");
        $this-> signers = CreditNote::parseSigners(Checks::checkParam($params, "signers"));
        $this-> externalId = Checks::checkParam($params, "externalId");
        $this-> streetLine1 = Checks::checkParam($params, "streetLine1");
        $this-> streetLine2 = Checks::checkParam($params, "streetLine2");
        $this-> district = Checks::checkParam($params, "district");
        $this-> city = Checks::checkParam($params, "city");
        $this-> stateCode = Checks::checkParam($params, "stateCode");
        $this-> zipCode = Checks::checkParam($params, "zipCode");
        $this-> paymentType = Checks::checkParam($params, "paymentType");
        $this-> rebateAmount = Checks::checkParam($params, "rebateAmount");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> expiration = Checks::checkParam($params, "expiration");
        $this-> amount = Checks::checkParam($params, "amount");
        $this-> documentId = Checks::checkParam($params, "documentId");
        $this-> status = Checks::checkParam($params, "status");
        $this-> transactionsIds = Checks::checkParam($params, "transactionsIds");
        $this-> workspaceId = Checks::checkParam($params, "workspaceId");
        $this-> taxAmount = Checks::checkParam($params, "taxAmount");
        $this-> interest = Checks::checkParam($params, "interest");
        $this-> nominalInterest = Checks::checkParam($params, "nominalInterest");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);

        list($this->payment, $this->paymentType) = self::parsePayment($this->payment, $this->paymentType);
    }

    public static function parseSigners($signers) {
        if (is_null($signers)) {
            return [];
        }
        $parsedSigners = [];
        foreach($signers as $signer) {
            if($signer instanceof CreditNote\Signer) {
                array_push($parsedSigners, $signer);
                continue;
            }
            $parsedSigner = function ($array) {
                $signerMaker = function ($array) {
                    return new CreditNote\Signer($array);
                };
                return API::fromApiJson($signerMaker, $array);
            };
            array_push($parsedSigners, $parsedSigner($signer));
        }    
        return $parsedSigners;
    }

    private static function parsePayment($payment, $paymentType)
    {
        if($payment instanceof CreditNote\Transfer)
            return [$payment, "transfer"];

        if(!is_array($payment))
            throw new \Exception("Payment must either be 
                a Transfer
                or an array.");

        $makerOptions = [
            "transfer" => function ($array) {
                return new CreditNote\Transfer($array);
            },
        ];

        if (isset($makerOptions[$paymentType]))
            $payment = API::fromApiJson($makerOptions[$paymentType], $payment);

        return [$payment, $paymentType];
    }

    /**
    # Create Credit Note

    Send an array of Credit Note objects for creation in the Stark Infra API

    ## Parameters (required):
        - notes [array of CreditNote objects]: array of Credit Note objects to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of Credit Note objects with updated attributes
     */
    public static function create($notes, $user = null)
    {
        return Rest::post($user, CreditNote::resource(), $notes);
    }

    /**
    # Retrieve a specific Credit Note

    Receive a single Credit Note object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - CreditNote object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, CreditNote::resource(), $id);
    }

    /**
    # Retrieve Credit Notes

    Receive an enumerator of CreditNote objects previously created in the Stark Infra API.
    Use this function instead of page if you want to stream the objects without worrying about cursors and pagination.

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "canceled", "created", "expired", "failed", "processing", "signed", "success"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of Credit Note objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        
        return Rest::getList($user, CreditNote::resource(), $options);
    }

    /**
    # Retrieve paged Credit Notes

    Receive a list of up to 100 Credit Note objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "canceled", "created", "expired", "failed", "processing", "signed", "success"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - list of Credit Note objects with updated attributes
        - cursor to retrieve the next page of CreditNote objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, CreditNote::resource(), $options);
    }

    /**
    # Cancel a Credit Note entity

    Cancel a Credit Note entity previously created in the Stark Infra API

    ## Parameters (required):
        - id [string]: Credit Note unique id. ex: "5656565656565656"
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - canceled Credit Note object
     */
    public static function cancel($id, $user = null)
    {
        return Rest::deleteId($user, CreditNote::resource(), $id);
    }

    private static function resource()
    {
        $note = function ($array) {
            return new CreditNote($array);
        };
        return [
            "name" => "CreditNote",
            "maker" => $note,
        ];
    }
}
