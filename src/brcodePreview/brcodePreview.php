<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkInfra\BrcodePreview\Subscription;


class BrcodePreview extends Resource
{

    public $payerId;
    public $endToEndId;
    public $accountNumber;
    public $accountType;
    public $amount;
    public $amountType;
    public $bankCode;
    public $branchCode;
    public $cashAmount;
    public $cashierBankCode;
    public $cashierType;
    public $discountAmount;
    public $due;
    public $fineAmount;
    public $interestAmount;
    public $keyId;
    public $name;
    public $nominalAmount;
    public $reconciliationId;
    public $reductionAmount;
    public $scheduled;
    public $status;
    public $subscription;
    public $taxId;

    /**
    # BrcodePreview object

    The BrcodePreview object is used to preview information from a BR Code before paying it.

    ## Parameters (required):
        - id [string]: BR Code string for the Pix payment. This is also de information directly encoded in a QR Code. ex: "00020126580014br.gov.bcb.pix0136a629532e-7693-4846-852d-1bbff817b5a8520400005303986540510.005802BR5908T'Challa6009Sao Paulo62090505123456304B14A"
        - payerId [string]: Tax id (CPF/CNPJ) of the individual or business requesting the PixKey information. This id is used by the Central Bank to limit request rates. ex: "20.018.183/0001-80"
        
    ## Parameters (optional):
        - endToEndId [string, default ""]: central bank's unique transaction ID. ex: "E79457883202101262140HHX553UPqeq"

    ## Attributes (return-only):
        - accountNumber [string]: Payment receiver account number. ex: "1234567"
        - accountType [string]: Payment receiver account type. ex: "checking"
        - amount [integer]: Value in cents that this payment is expecting to receive. If 0, any value is accepted. ex: 123 (= R$1,23)
        - amountType [string]: Amount type of the BR Code. If the amount type is "custom" the BR Code's amount can be changed by the sender at the moment of payment. Options: "fixed" or "custom"
        - bankCode [string]: Payment receiver bank code. ex: "20018183"
        - branchCode [string]: Payment receiver branch code. ex: "0001"
        - cashAmount [integer]: Amount to be withdrawn from the cashier in cents. ex: 1000 (= R$ 10.00)
        - cashierBankCode [string]: Cashier's bank code. ex: "20018183"
        - cashierType [string]: Cashier's type. Options: "merchant", "participant" and "other"
        - discountAmount [integer]: Discount value calculated over nominalAmount. ex: 3000
        - due [DateTime]: BR Code due date
        - fineAmount [integer]: Fine value calculated over nominalAmount. ex: 20000
        - interestAmount [integer]: Interest value calculated over nominalAmount. ex: 10000
        - keyId [string]: Receiver's PixKey id. ex: "+5511989898989"
        - name [string]: Payment receiver name. ex: "Tony Stark"
        - nominalAmount [integer]: Brcode emission amount, without fines, fees and discounts. ex: 1234 (= R$ 12.34)
        - reconciliationId [string]: Reconciliation ID linked to this payment. If the brcode is dynamic, the reconciliationId will have from 26 to 35 alphanumeric characters, ex: "cd65c78aeb6543eaaa0170f68bd741ee". If the brcode is static, the reconciliationId will have up to 25 alphanumeric characters "ah27s53agj6493hjds6836v49"
        - reductionAmount [integer]: Reduction value to discount from nominalAmount. ex: 1000
        - scheduled [DateTime]: Date of payment execution.
        - status [string]: Payment status. ex: "active", "paid", "canceled" or "unknown"
        - subscription [Subscription object]: BR code subscription information
        - taxId [string]: Payment receiver tax ID. ex: "012.345.678-90"
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->payerId = Checks::checkParam($params, "payerId");
        $this->endToEndId = Checks::checkParam($params, "endToEndId");
        $this->accountNumber = Checks::checkParam($params, "accountNumber");
        $this->accountType = Checks::checkParam($params, "accountType");
        $this->amount = Checks::checkParam($params, "amount");
        $this->amountType = Checks::checkParam($params, "amountType");
        $this->bankCode = Checks::checkParam($params, "bankCode");
        $this->branchCode = Checks::checkParam($params, "branchCode");
        $this->cashAmount = Checks::checkParam($params, "cashAmount");
        $this->cashierBankCode = Checks::checkParam($params, "cashierBankCode");
        $this->cashierType = Checks::checkParam($params, "cashierType");
        $this->discountAmount = Checks::checkParam($params, "discountAmount");
        $this->due = empty($params['due']) ? null : Checks::checkDateTime(Checks::checkParam($params, "due"));
        $this->fineAmount = Checks::checkParam($params, "fineAmount");
        $this->interestAmount = Checks::checkParam($params, "interestAmount");
        $this->keyId = Checks::checkParam($params, "keyId");
        $this->name = Checks::checkParam($params, "name");
        $this->nominalAmount = Checks::checkParam($params, "nominalAmount");
        $this->reconciliationId = Checks::checkParam($params, "reconciliationId");
        $this->reductionAmount = Checks::checkParam($params, "reductionAmount");
        $this->scheduled = Checks::checkDateTime(Checks::checkParam($params, "scheduled"));
        $this->status = Checks::checkParam($params, "status");
        $this->subscription = Subscription::parseSubscription(Checks::checkParam($params, "subscription"));
        $this->taxId = Checks::checkParam($params, "taxId");

        Checks::checkParams($params);
    }

    /**
    # Retrieve BrcodePreviews

    Process BR Codes before paying them.

    ## Parameters (required):
        - previews [array of BrcodePreview objects]: Array of BrcodePreview objects to preview. ex: [starkinfra\BrcodePreview("00020126580014br.gov.bcb.pix0136a629532e-7693-4846-852d-1bbff817b5a8520400005303986540510.005802BR5908T'Challa6009Sao Paulo62090505123456304B14A")]

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of BrcodePreview objects with updated attributes
     */
    public static function create($previews, $user = null)
    {
        return Rest::post($user, BrcodePreview::resource(), $previews);
    }

    private static function resource()
    {
        $preview = function ($array) {
            return new BrcodePreview($array);
        };
        return [
            "name" => "BrcodePreview",
            "maker" => $preview,
        ];
    }
}
