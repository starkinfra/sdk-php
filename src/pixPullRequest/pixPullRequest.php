<?php

namespace StarkInfra;
use StarkCore\Utils\API;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class PixPullRequest extends Resource
{

    public $amount;
    public $due;
    public $endToEndId;
    public $receiverAccountNumber;
    public $receiverAccountType;
    public $receiverBankCode;
    public $reconciliationId;
    public $subscriptionId;
    public $attemptType;
    public $description;
    public $receiverBranchCode;
    public $tags;
    public $status;
    public $flow;
    public $receiverName;
    public $receiverTaxId;
    public $senderBankCode;
    public $senderFinalName;
    public $senderTaxId;
    public $subscriptionBacenId;
    public $created;
    public $updated;

    /**
    # PixPullRequest object

    A Pix Pull Request is a command sent to the payer's bank to trigger the automatic
    debit linked to an active PixPullSubscription.

    ## Parameters (required):
        - amount [integer]: amount to be charged in cents.
        - due [string]: due date for answering with an approval or denial. ISO 8601.
        - endToEndId [string]: Central Bank's unique transaction id.
        - receiverAccountNumber [string]: receiver's bank account number.
        - receiverAccountType [string]: Options: "checking", "savings", "salary", "payment".
        - receiverBankCode [string]: receiver's bank code.
        - reconciliationId [string]: id used for conciliation of the resulting Pix transaction.
        - subscriptionId [string]: unique id of the parent PixPullSubscription.

    ## Parameters (optional):
        - attemptType [string, default null]: Options: "default", "instantRetry", "scheduledRetry".
        - description [string, default null]: additional information.
        - receiverBranchCode [string, default null]
        - tags [array of strings, default null]

    ## Attributes (return-only):
        - id [string]
        - status [string]: Options: "created", "active", "canceled", "failed".
        - flow [string]: Options: "in", "out".
        - receiverName [string]
        - receiverTaxId [string]
        - senderBankCode [string]
        - senderFinalName [string]
        - senderTaxId [string]
        - subscriptionBacenId [string]
        - created [DateTime]
        - updated [DateTime]
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> amount = Checks::checkParam($params, "amount");
        $this-> due = Checks::checkDateTime(Checks::checkParam($params, "due"));
        $this-> endToEndId = Checks::checkParam($params, "endToEndId");
        $this-> receiverAccountNumber = Checks::checkParam($params, "receiverAccountNumber");
        $this-> receiverAccountType = Checks::checkParam($params, "receiverAccountType");
        $this-> receiverBankCode = Checks::checkParam($params, "receiverBankCode");
        $this-> reconciliationId = Checks::checkParam($params, "reconciliationId");
        $this-> subscriptionId = Checks::checkParam($params, "subscriptionId");
        $this-> attemptType = Checks::checkParam($params, "attemptType");
        $this-> description = Checks::checkParam($params, "description");
        $this-> receiverBranchCode = Checks::checkParam($params, "receiverBranchCode");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> status = Checks::checkParam($params, "status");
        $this-> flow = Checks::checkParam($params, "flow");
        $this-> receiverName = Checks::checkParam($params, "receiverName");
        $this-> receiverTaxId = Checks::checkParam($params, "receiverTaxId");
        $this-> senderBankCode = Checks::checkParam($params, "senderBankCode");
        $this-> senderFinalName = Checks::checkParam($params, "senderFinalName");
        $this-> senderTaxId = Checks::checkParam($params, "senderTaxId");
        $this-> subscriptionBacenId = Checks::checkParam($params, "subscriptionBacenId");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    public static function create($requests, $user = null)
    {
        return Rest::post($user, PixPullRequest::resource(), $requests);
    }

    public static function get($id, $user = null)
    {
        return Rest::getId($user, PixPullRequest::resource(), $id);
    }

    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, PixPullRequest::resource(), $options);
    }

    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, PixPullRequest::resource(), $options);
    }

    /**
    # Update PixPullRequest

    Change status to "scheduled" or "denied". When denying, `reason` is required:
    "senderAccountClosed", "senderAccountBlocked", "amountNotAllowed".
     */
    public static function update($id, $params = [], $user = null)
    {
        return Rest::patchId($user, PixPullRequest::resource(), $id, $params);
    }

    /**
    # Cancel PixPullRequest

    `reason` is sent as a query parameter on the DELETE request via deleteRaw.
     */
    public static function cancel($id, $reason, $user = null)
    {
        $resource = PixPullRequest::resource();
        $path = "/" . API::endpoint($resource["name"]) . "/" . $id;
        $response = Rest::deleteRaw($user, $path, null, null, true, ["reason" => $reason]);
        $json = $response->json();
        $entity = $json[API::lastName($resource["name"])];
        return API::fromApiJson($resource["maker"], $entity);
    }

    private static function resource()
    {
        $request = function ($array) {
            return new PixPullRequest($array);
        };
        return [
            "name" => "PixPullRequest",
            "maker" => $request,
        ];
    }
}
