<?php

namespace StarkInfra\PixDispute;
use StarkCore\Utils\API;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class Transaction extends SubResource
{

    public $endToEndId;
    public $amount;
    public $nominalAmount;
    public $receiverType;
    public $receiverTaxIdCreated;
    public $receiverAccountCreated;
    public $receiverBankCode;
    public $receiverId;
    public $senderType;
    public $senderTaxIdCreated;
    public $senderAccountCreated;
    public $senderBankCode;
    public $senderId;
    public $settled;

    /**
    # PixDispute\Transaction object
    Transaction object related to the PixDispute.

    ## Attributes (return-only):
        - endToEndId [string]: Central Bank's unique transaction id. ex: "E79457883202101262140HHX553UPqeq"
        - amount [integer]: refundable amount. ex: 11234 (= R$ 112.34)
        - nominalAmount [integer]: transaction amount. ex: 11234 (= R$ 112.34)
        - receiverType [string]: receiver person type. Options: "individual", "business"
        - receiverTaxIdCreated [string]: receiver's taxId creation date. For business type only.
        - receiverAccountCreated [string]: receiver's account creation date.
        - receiverBankCode [string]: receiver's bank code. ex: "20018183"
        - receiverId [string]: identifier of accountholder in the graph.
        - senderType [string]: sender person type. Options: "individual", "business"
        - senderTaxIdCreated [string]: sender's taxId creation date. For business type only.
        - senderAccountCreated [string]: sender's account creation date.
        - senderBankCode [string]: sender's bank code. ex: "20018183"
        - senderId [string]: identifier of accountholder in the graph.
        - settled [DateTime]: settled datetime of the transaction in ISO format.
     */

    function __construct(array $params)
    {
        $this-> endToEndId = Checks::checkParam($params, "endToEndId");
        $this-> amount = Checks::checkParam($params, "amount");
        $this-> nominalAmount = Checks::checkParam($params, "nominalAmount");
        $this-> receiverType = Checks::checkParam($params, "receiverType");
        $this-> receiverTaxIdCreated = Checks::checkParam($params, "receiverTaxIdCreated");
        $this-> receiverAccountCreated = Checks::checkParam($params, "receiverAccountCreated");
        $this-> receiverBankCode = Checks::checkParam($params, "receiverBankCode");
        $this-> receiverId = Checks::checkParam($params, "receiverId");
        $this-> senderType = Checks::checkParam($params, "senderType");
        $this-> senderTaxIdCreated = Checks::checkParam($params, "senderTaxIdCreated");
        $this-> senderAccountCreated = Checks::checkParam($params, "senderAccountCreated");
        $this-> senderBankCode = Checks::checkParam($params, "senderBankCode");
        $this-> senderId = Checks::checkParam($params, "senderId");
        $this-> settled = Checks::checkDateTime(Checks::checkParam($params, "settled"));

        Checks::checkParams($params);
    }

    public static function parseTransactions($transactions) {
        if ($transactions == null) {
            return null;
        }
        $parsedTransactions = [];
        foreach($transactions as $transaction) {
            if($transaction instanceof Transaction) {
                array_push($parsedTransactions, $transaction);
                continue;
            }
            $parsedTransaction = function ($array) {
                $transactionMaker = function ($array) {
                    return new Transaction($array);
                };
                return API::fromApiJson($transactionMaker, $array);
            };
            array_push($parsedTransactions, $parsedTransaction($transaction));
        }
        return $parsedTransactions;
    }
}
