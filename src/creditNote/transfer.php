<?php

namespace StarkInfra\CreditNote;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class Transfer extends Resource
{

    public $taxId;
    public $amount;
    public $name;
    public $bankCode;
    public $branchCode;
    public $accountNumber;
    public $accountType;
    public $externalId;
    public $scheduled;
    public $description;
    public $tags;
    public $fee;
    public $status;
    public $created;
    public $updated;
    public $transactionIds;

    /**
    # CreditNote\Transfer object

    Transfer object to be created and sent to the credit receiver.

    ## Parameters (required):
    - name [string]: receiver full name. ex: "Anthony Edward Stark"
    - taxId [string]: receiver tax ID (CPF or CNPJ) with or without formatting. ex: "01234567890" or "20.018.183/0001-80"
    - bankCode [string]: code of the receiver bank institution in Brazil. If an ISPB (8 digits) is informed, a Pix transfer will be created, else a TED will be issued. ex: "20018183" or "341"
    - branchCode [string]: receiver bank account branch. Use '-' in case there is a verifier digit. ex: "1357-9"
    - accountNumber [string]: receiver bank account number. Use '-' before the verifier digit. ex: "876543-2"

    ## Parameters (optional):
    - accountType [string, default "checking"]: Receiver bank account type. This parameter only has effect on Pix Transfers. ex: "checking", "savings", "salary" or "payment"
    - tags [array of strings, default null]: list of strings for reference when searching for transfers. ex: ["employees", "monthly"]

    ## Attributes (return-only):
    - id [string]: unique id returned when the transfer is created. ex: "5656565656565656"
    - amount [integer]: amount in cents to be transferred. ex: 1234 (= R$ 12.34)
    - externalId [string]: url safe string that must be unique among all your transfers. Duplicated externalIds will cause failures. By default, this parameter will block any transfer that repeats amount and receiver information on the same date. ex: "my-internal-id-123456"
    - scheduled [DateTime or string]: date or datetime when the transfer will be processed. May be pushed to next business day if necessary. ex: "2020-03-10 10:30:00.000"
    - description [string]: optional description to override default description to be shown in the bank statement. ex: "Payment for service #1234"
    - fee [integer]: fee charged when the Transfer is processed. ex: 200 (= R$ 2.00)
    - status [string]: current transfer status. ex: "success" or "failed"
    - transactionIds [array of strings]: ledger Transaction IDs linked to this Transfer (if there are two, the second is the chargeback). ex: ["19827356981273"]
    - created [DateTime]: creation datetime for the Transfer.
    - updated [DateTime]: latest update datetime for the Transfer.
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> name = Checks::checkParam($params, "name");
        $this-> taxId = Checks::checkParam($params, "taxId");
        $this-> bankCode = Checks::checkParam($params, "bankCode");
        $this-> branchCode = Checks::checkParam($params, "branchCode");
        $this-> accountNumber = Checks::checkParam($params, "accountNumber");
        $this-> accountType = Checks::checkParam($params, "accountType");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> amount = Checks::checkParam($params, "amount");
        $this-> externalId = Checks::checkParam($params, "externalId");
        $this-> scheduled = Checks::checkDateTime(Checks::checkParam($params, "scheduled"));
        $this-> description = Checks::checkParam($params, "description");
        $this-> fee = Checks::checkParam($params, "fee");
        $this-> status = Checks::checkParam($params, "status");
        $this-> transactionIds = Checks::checkParam($params, "transactionIds");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }
}
