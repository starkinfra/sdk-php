<?php

namespace StarkInfra;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\StarkDate;
use StarkInfra\Utils\Parse;


class PixRequest extends Resource
{
    /**
    # PixRequest object

    When you initialize a PixRequest, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the array of created objects.

    ## Parameters (required):
        -amount [integer]: amount in cents to be requestred. ex: 11234 (= R$ 112.34)
        -externalId [string]: url safe string that must be unique among all your PixRequests. Duplicated external IDs will cause failures. By default, this parameter will block any PixRequests that repeats amount and receiver information on the same date. ex: "my-internal-id-123456"
        -senderName [string]: sender's full name. ex: "Anthony Edward Stark"
        -senderTaxId [string]: sender's tax ID (CPF or CNPJ) with or without formatting. ex: "01234567890" or "20.018.183/0001-80"
        -senderBranchCode [string]: sender's bank account branch code. Use '-' in case there is a verifier digit. ex: "1357-9"
        -senderAccountNumber [string]: sender's bank account number. Use '-' before the verifier digit. ex: "876543-2"
        -senderAccountType [string, default "checking"]: sender's bank account type. ex: "checking", "savings", "salary" or "payment"
        -receiverName [string]: receiver's full name. ex: "Anthony Edward Stark"
        -receiverTaxId [string]: receiver's tax ID (CPF or CNPJ) with or without formatting. ex: "01234567890" or "20.018.183/0001-80"
        -receiverBankCode [string]: receiver's bank institution code in Brazil. ex: "20018183" or "341"
        -receiverAccountNumber [string]: receiver's bank account number. Use '-' before the verifier digit. ex: "876543-2"
        -receiverBranchCode [string]: receiver's bank account branch code. Use '-' in case there is a verifier digit. ex: "1357-9"
        -receiverAccountType [string]: receiver's bank account type. ex: "checking", "savings", "salary" or "payment"
        -endToEndId [string]: central bank's unique transaction ID. ex: "E79457883202101262140HHX553UPqeq"

    ## Parameters (optional):
        -receiverKeyId [string, default null]: receiver's dict key. ex: "20.018.183/0001-80"
        -description [string, default null]: optional description to override default description to be shown in the bank statement. ex: "Payment for service #1234"
        -reconciliationId [string, default null]: Reconciliation ID linked to this payment. ex: "b77f5236-7ab9-4487-9f95-66ee6eaf1781"
        -initiatorTaxId [string, default null]: Payment initiator's tax id (CPF/CNPJ). ex: "01234567890" or "20.018.183/0001-80"
        -cashAmount [Long, default 0]: Amount to be withdrawal from the cashier in cents. ex: 1000 (= R$ 10.00)
        -cashierBankCode [string, default null]: Cashier's bank code. ex: "00000000"
        -cashierType [string, default null]: Cashier's type. ex: [merchant, other, participant]
        -tags [list of strings, default null]: list of strings for reference when searching for PixRequests. ex: ["employees", "monthly"]
        -method [string, default null]: execution  method for thr creation of the PIX. ex: "manual", "payerQrcode", "dynamicQrcode".

    ## Attributes (return-only):
        -id [string]: unique id returned when the PixRequest is created. ex: "5656565656565656"
        -fee [integer]: fee charged when PixRequest is paid. ex: 200 (= R$ 2.00)
        -status [string]: current PixRequest status. ex: "registered" or "paid"
        -flow [string]: direction of money flow. ex: "in" or "out"
        -senderBankCode [string]: sender's bank institution code in Brazil. If an ISPB (8 digits) is informed. ex: "20018183" or "341"
        -created [DateTime]: created datetime for the PixRequest. 
        -updated [DateTime]: update datetime for the PixRequest. 
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> amount = Checks::checkParam($params, "amount");
        $this-> externalId = Checks::checkParam($params, "externalId");
        $this-> senderName = Checks::checkParam($params, "senderName");
        $this-> senderTaxId = Checks::checkParam($params, "senderTaxId");
        $this-> senderBranchCode = Checks::checkParam($params, "senderBranchCode");
        $this-> senderAccountNumber = Checks::checkParam($params, "senderAccountNumber");
        $this-> senderAccountType = Checks::checkParam($params, "senderAccountType");
        $this-> receiverName = Checks::checkParam($params, "receiverName");
        $this-> receiverTaxId = Checks::checkParam($params, "receiverTaxId");
        $this-> receiverBankCode = Checks::checkParam($params, "receiverBankCode");
        $this-> receiverAccountNumber = Checks::checkParam($params, "receiverAccountNumber");
        $this-> receiverBranchCode = Checks::checkParam($params, "receiverBranchCode");
        $this-> receiverAccountType = Checks::checkParam($params, "receiverAccountType");
        $this-> endToEndId = Checks::checkParam($params, "endToEndId");
        $this-> receiverKeyId = Checks::checkParam($params, "receiverKeyId");
        $this-> description = Checks::checkParam($params, "description");
        $this-> reconciliationId = Checks::checkParam($params, "reconciliationId");
        $this-> initiatorTaxId = Checks::checkParam($params, "initiatorTaxId");
        $this-> cashAmount = Checks::checkParam($params, "cashAmount");
        $this-> cashierBankCode = Checks::checkParam($params, "cashierBankCode");
        $this-> cashierType = Checks::checkParam($params, "cashierType");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> method = Checks::checkParam($params, "method");
        $this-> fee = Checks::checkParam($params, "fee");
        $this-> status = Checks::checkParam($params, "status");
        $this-> flow = Checks::checkParam($params, "flow");
        $this-> senderBankCode = Checks::checkParam($params, "senderBankCode");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create PixRequests

    Send an array of PixRequest objects for creation in the Stark Infra API

    ## Parameters (required):
        - requests [array of PixRequest objects]: array of PixRequest objects to be created in the API.
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of PixRequest objects with updated attributes
     */
    public static function create($requests, $user = null)
    {
        return Rest::post($user, PixRequest::resource(), $requests);
    }

    /**
    # Retrieve a specific PixRequest

    Receive a single PixRequest object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - PixRequest object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, PixRequest::resource(), $id);
    }

    /**
    # Retrieve PixRequests

    Receive a enumerator of PixRequest objects previously created in the Stark Infra API

    ## Parameters (optional):
        - fields [list of strings, default null]: parameters to be retrieved from PixRequest objects. ex: ["amount", "id"]
        - limit [integer, default 100]: maximum number of objects to be retrieved. ex: 35
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "paid" or "registered"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - endToEndIds [list of strings, default null]: central bank's unique transaction IDs. ex: ["E79457883202101262140HHX553UPqeq", "E79457883202101262140HHX553UPxzx"]
        - externalIds [list of strings, default null]: url safe strings that must be unique among all your PixRequests. Duplicated external IDs will cause failures. By default, this parameter will block any PixRequests that repeats amount and receiver information on the same date. ex: ["my-internal-id-123456", "my-internal-id-654321"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of PixRequest objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, PixRequest::resource(), $options);
    }

    /**
    # Retrieve paged PixRequests

    Receive a list of up to 100 PixRequest objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - fields [list of strings, default null]: parameters to be retrieved from PixRequest objects. ex: ["amount", "id"]
        - limit [integer, default 100]: maximum number of objects to be retrieved. ex: 35
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "paid" or "registered"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - endToEndIds [list of strings, default null]: central bank's unique transaction IDs. ex: ["E79457883202101262140HHX553UPqeq", "E79457883202101262140HHX553UPxzx"]
        - externalIds [list of strings, default null]: url safe strings that must be unique among all your PixRequests. Duplicated external IDs will cause failures. By default, this parameter will block any PixRequests that repeats amount and receiver information on the same date. ex: ["my-internal-id-123456", "my-internal-id-654321"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of PixRequest objects with updated attributes
        - cursor to retrieve the next page of PixRequest objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, PixRequest::resource(), $options);
    }

    /**
    # Parse PixRequests

    Create a single PixRequest object from a content string received from a handler listening at the request url.
    If the provided digital signature does not check out with the Stark public key, a
    stark.error.InvalidSignatureError will be raised.

    ## Parameters (required):
        - content [string]: response content from request received at user endpoint (not parsed)
        - signature [string]: base-64 digital signature received at response header "Digital-Signature"

    ## Parameters (required):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call

    ## Return:
        - Parsed PixRequest object
     */
    public static function parse($content, $signature, $user = null)
    {
        return Parse::parseAndVerify($content, $signature, PixRequest ::resource(), $user);
    }

    private static function resource()
    {
        $request = function ($array) {
            return new PixRequest($array);
        };
        return [
            "name" => "PixRequest",
            "maker" => $request,
        ];
    }
}
