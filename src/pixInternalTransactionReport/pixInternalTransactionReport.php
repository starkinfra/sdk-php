<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class PixInternalTransactionReport extends Resource
{

    public $amount;
    public $created;
    public $endToEndId;
    public $method;
    public $referenceType;
    public $senderAccountNumber;
    public $senderBranchCode;
    public $senderAccountType;
    public $senderBankCode;
    public $senderTaxId;
    public $receiverAccountNumber;
    public $receiverBranchCode;
    public $receiverAccountType;
    public $receiverBankCode;
    public $receiverTaxId;
    public $receiverKeyId;
    public $returnId;
    public $status;
    public $updated;

    /**
    # PixInternalTransactionReport object

    Transactions that happen internally, outside of the SPI, must be reported to
    the Central Bank so they are reflected in the participant's statements. A
    PixInternalTransactionReport is the report you create for each such transaction.

    When you initialize a PixInternalTransactionReport, the entity will not be
    automatically created in the Stark Infra API. The 'create' function sends the
    objects to the Stark Infra API and returns the list of created objects.

    ## Parameters (required):
        - amount [integer]: amount in cents of the reported transaction. ex: 1234 (= R$ 12.34)
        - created [DateTime or string]: datetime when the reported transaction occurred. ex: "2026-06-16T17:23:53.980238+00:00"
        - endToEndId [string]: central bank's unique transaction id. ex: "E20018183202201201213u34sav898j"
        - method [string]: execution method of the reported Pix. Options: "contactless", "dict", "dynamicQrcode", "initiator", "manual", "payerQrcode", "staticContactless", "staticQrcode", "subscription"
        - referenceType [string]: type of the reported transaction. Options: "request", "reversal"
        - senderAccountNumber [string]: sender's bank account number. ex: "76543-8"
        - senderBranchCode [string]: sender's bank account branch code. ex: "2201"
        - senderAccountType [string]: sender's bank account type. Options: "checking", "savings", "salary", "payment"
        - senderBankCode [string]: sender's participant code (ISPB, 8 digits). ex: "00000665"
        - senderTaxId [string]: sender's tax ID (CPF or CNPJ) with or without formatting. ex: "01234567890" or "20.018.183/0001-80"
        - receiverAccountNumber [string]: receiver's bank account number. ex: "00000-1"
        - receiverBranchCode [string]: receiver's bank account branch code. ex: "0001"
        - receiverAccountType [string]: receiver's bank account type. Options: "checking", "savings", "salary", "payment"
        - receiverBankCode [string]: receiver's participant code (ISPB, 8 digits). ex: "18236120"
        - receiverTaxId [string]: receiver's tax ID (CPF or CNPJ) with or without formatting. ex: "01234567890" or "20.018.183/0001-80"

    ## Parameters (optional):
        - receiverKeyId [string, default null]: receiver's Pix Key. ex: "+5511989898989"
        - returnId [string, default null]: central bank's unique reversal id. Required when referenceType is "reversal". ex: "D20018183202201201213u34sav898j"

    ## Attributes (return-only):
        - id [string]: unique id returned when the PixInternalTransactionReport is created. ex: "5656565656565656"
        - status [string]: current PixInternalTransactionReport status. ex: "created", "failed", "sent", "success"
        - updated [DateTime]: latest update datetime for the PixInternalTransactionReport.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> amount = Checks::checkParam($params, "amount");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> endToEndId = Checks::checkParam($params, "endToEndId");
        $this-> method = Checks::checkParam($params, "method");
        $this-> referenceType = Checks::checkParam($params, "referenceType");
        $this-> senderAccountNumber = Checks::checkParam($params, "senderAccountNumber");
        $this-> senderBranchCode = Checks::checkParam($params, "senderBranchCode");
        $this-> senderAccountType = Checks::checkParam($params, "senderAccountType");
        $this-> senderBankCode = Checks::checkParam($params, "senderBankCode");
        $this-> senderTaxId = Checks::checkParam($params, "senderTaxId");
        $this-> receiverAccountNumber = Checks::checkParam($params, "receiverAccountNumber");
        $this-> receiverBranchCode = Checks::checkParam($params, "receiverBranchCode");
        $this-> receiverAccountType = Checks::checkParam($params, "receiverAccountType");
        $this-> receiverBankCode = Checks::checkParam($params, "receiverBankCode");
        $this-> receiverTaxId = Checks::checkParam($params, "receiverTaxId");
        $this-> receiverKeyId = Checks::checkParam($params, "receiverKeyId");
        $this-> returnId = Checks::checkParam($params, "returnId");
        $this-> status = Checks::checkParam($params, "status");
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create PixInternalTransactionReports

    Send an array of PixInternalTransactionReport objects for creation in the Stark Infra API

    ## Parameters (required):
        - reports [array of PixInternalTransactionReport objects]: array of PixInternalTransactionReport objects to be created in the API.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of PixInternalTransactionReport objects with updated attributes
     */
    public static function create($reports, $user = null)
    {
        return Rest::post($user, PixInternalTransactionReport::resource(), $reports);
    }

    /**
    # Retrieve a specific PixInternalTransactionReport

    Receive a single PixInternalTransactionReport object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - PixInternalTransactionReport object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, PixInternalTransactionReport::resource(), $id);
    }

    /**
    # Retrieve PixInternalTransactionReports

    Receive an enumerator of PixInternalTransactionReport objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null]: date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. ex: ["success", "failed"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of PixInternalTransactionReport objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, PixInternalTransactionReport::resource(), $options);
    }

    /**
    # Retrieve paged PixInternalTransactionReports

    Receive a list of up to 100 PixInternalTransactionReport objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null]: date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created only before specified date. ex: "2020-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. ex: ["success", "failed"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of PixInternalTransactionReport objects with updated attributes
        - cursor to retrieve the next page of PixInternalTransactionReport objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, PixInternalTransactionReport::resource(), $options);
    }

    private static function resource()
    {
        $report = function ($array) {
            return new PixInternalTransactionReport($array);
        };
        return [
            "name" => "PixInternalTransactionReport",
            "maker" => $report,
        ];
    }
}
