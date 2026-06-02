<?php

namespace StarkInfra\BrcodePreview;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;

class Subscription extends SubResource
{
    public $amount;
    public $amountMinLimit;
    public $bacenId;
    public $created;
    public $description;
    public $installmentEnd;
    public $installmentStart;
    public $interval;
    public $pullRetryLimit;
    public $receiverBankCode;
    public $receiverName;
    public $receiverTaxId;
    public $referenceCode;
    public $senderFinalName;
    public $senderFinalTaxId;
    public $status;
    public $type;
    public $updated;

    /**
    # BrcodePreview\Subscription object
    Subscription is a recurring payment that can be used to charge a user periodically.

    ## Parameters (optional):
        - amount [integer]: amount to be charged in cents. ex: 1000 (= R$ 10.00)
        - amountMinLimit [integer]: minimum amount limit for the subscription. ex: 500 (= R$ 5.00)
        - bacenId [string]: BACEN (Brazilian Central Bank) identifier.
        - created [DateTime or string]: creation datetime for the subscription.
        - description [string]: description of the subscription.
        - installmentEnd [DateTime or string]: end datetime for the installments.
        - installmentStart [DateTime or string]: start datetime for the installments.
        - interval [string]: interval for the recurring charge. ex: "monthly"
        - pullRetryLimit [integer]: maximum number of retries for pulling the payment.
        - receiverBankCode [string]: bank code of the receiver.
        - receiverName [string]: name of the receiver.
        - receiverTaxId [string]: tax ID of the receiver.
        - referenceCode [string]: reference code for the subscription.
        - senderFinalName [string]: final sender name.
        - senderFinalTaxId [string]: final sender tax ID.
        - status [string]: current status of the subscription.
        - type [string]: type of the subscription.
        - updated [DateTime or string]: last update datetime for the subscription.
    */

    function __construct(array $params)
    {
        $this->amount = Checks::checkParam($params, "amount");
        $this->amountMinLimit = Checks::checkParam($params, "amountMinLimit");
        $this->bacenId = Checks::checkParam($params, "bacenId");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->description = Checks::checkParam($params, "description");
        $this->installmentEnd = Checks::checkDateTime(Checks::checkParam($params, "installmentEnd"));
        $this->installmentStart = Checks::checkDateTime(Checks::checkParam($params, "installmentStart"));
        $this->interval = Checks::checkParam($params, "interval");
        $this->pullRetryLimit = Checks::checkParam($params, "pullRetryLimit");
        $this->receiverBankCode = Checks::checkParam($params, "receiverBankCode");
        $this->receiverName = Checks::checkParam($params, "receiverName");
        $this->receiverTaxId = Checks::checkParam($params, "receiverTaxId");
        $this->referenceCode = Checks::checkParam($params, "referenceCode");
        $this->senderFinalName = Checks::checkParam($params, "senderFinalName");
        $this->senderFinalTaxId = Checks::checkParam($params, "senderFinalTaxId");
        $this->status = Checks::checkParam($params, "status");
        $this->type = Checks::checkParam($params, "type");
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));
    }

    public static function parseSubscription($subscription)
    {
        if (is_null($subscription)) {
            return null;
        }
        if (empty((array)$subscription)) {
            return null;
        }
        if ($subscription instanceof Subscription) {
            return $subscription;
        }
        $subscriptionMaker = function ($array) {
            return new Subscription($array);
        };
        return \StarkCore\Utils\API::fromApiJson($subscriptionMaker, $subscription);
    }

    public static function resource()
    {
        $subscription = function ($array) {
            return new Subscription($array);
        };
        return [
            "name" => "Subscription",
            "maker" => $subscription,
        ];
    }
}