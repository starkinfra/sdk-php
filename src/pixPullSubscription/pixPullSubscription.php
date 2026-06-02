<?php

namespace StarkInfra;
use StarkCore\Utils\API;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Parse;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class PixPullSubscription extends Resource
{

    public $bacenId;
    public $externalId;
    public $installmentStart;
    public $interval;
    public $receiverName;
    public $receiverTaxId;
    public $senderAccountNumber;
    public $senderBankCode;
    public $senderBranchCode;
    public $senderTaxId;
    public $type;
    public $amount;
    public $amountMinLimit;
    public $description;
    public $due;
    public $installmentEnd;
    public $receiverBankCode;
    public $referenceCode;
    public $pullRetryLimit;
    public $senderCityCode;
    public $senderFinalName;
    public $senderFinalTaxId;
    public $tags;
    public $status;
    public $flow;
    public $created;
    public $updated;

    /**
    # PixPullSubscription object

    A recurring Pix debit authorization. It defines the frequency, amount, and required payer
    authorizations for a series of Pix debits to be pulled from the sender by the receiver.

    ## Parameters (required):
        - bacenId [string]: Central Bank's unique recurrency id.
        - externalId [string]: safe string unique among all your subscriptions.
        - installmentStart [string]: start datetime of settlements. ISO 8601.
        - interval [string]: cycle definition. Options: "week", "month", "quarter", "semester", "year".
        - receiverName [string]: receiver's full name.
        - receiverTaxId [string]: receiver's tax ID (CPF or CNPJ).
        - senderAccountNumber [string]: sender's bank account number.
        - senderBankCode [string]: sender's bank institution code.
        - senderBranchCode [string]: sender's bank account branch code.
        - senderTaxId [string]: sender's tax ID.
        - type [string]: subscription journey type. Options: "push", "subscriptionAndPayment".

    ## Parameters (optional):
        - amount [integer, default null]: amount in cents charged every cycle.
        - amountMinLimit [integer, default null]: floor value for variable-amount subscriptions.
        - description [string, default null]: additional information delivered to the sender.
        - due [string, default null]: due date for the sender's answer. Empty string is normalized to null.
        - installmentEnd [string, default null]: end datetime of settlements. Empty string is normalized to null.
        - receiverBankCode [string, default null]: receiver's bank institution code.
        - referenceCode [string, default null]: commercial-relation identifier.
        - pullRetryLimit [integer, default null]: max retries per failed pull cycle.
        - senderCityCode [string, default null]: IBGE code. Required when patching status to "confirmed".
        - senderFinalName [string, default null]: final sender name.
        - senderFinalTaxId [string, default null]: final sender tax ID.
        - tags [array of strings, default null]: list of strings for reference.

    ## Attributes (return-only):
        - id [string]: unique id assigned on create.
        - status [string]: current lifecycle state. Options: "created", "active", "canceled", "failed".
        - flow [string]: direction of money flow. Options: "in", "out".
        - created [DateTime]: creation datetime.
        - updated [DateTime]: latest update datetime.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> bacenId = Checks::checkParam($params, "bacenId");
        $this-> externalId = Checks::checkParam($params, "externalId");
        $this-> installmentStart = Checks::checkDateTime(Checks::checkParam($params, "installmentStart"));
        $this-> interval = Checks::checkParam($params, "interval");
        $this-> receiverName = Checks::checkParam($params, "receiverName");
        $this-> receiverTaxId = Checks::checkParam($params, "receiverTaxId");
        $this-> senderAccountNumber = Checks::checkParam($params, "senderAccountNumber");
        $this-> senderBankCode = Checks::checkParam($params, "senderBankCode");
        $this-> senderBranchCode = Checks::checkParam($params, "senderBranchCode");
        $this-> senderTaxId = Checks::checkParam($params, "senderTaxId");
        $this-> type = Checks::checkParam($params, "type");
        $this-> amount = Checks::checkParam($params, "amount");
        $this-> amountMinLimit = Checks::checkParam($params, "amountMinLimit");
        $this-> description = Checks::checkParam($params, "description");
        $due = Checks::checkParam($params, "due");
        if ($due === "") { $due = null; }
        $this-> due = Checks::checkDateTime($due);
        $installmentEnd = Checks::checkParam($params, "installmentEnd");
        if ($installmentEnd === "") { $installmentEnd = null; }
        $this-> installmentEnd = Checks::checkDateTime($installmentEnd);
        $this-> receiverBankCode = Checks::checkParam($params, "receiverBankCode");
        $this-> referenceCode = Checks::checkParam($params, "referenceCode");
        $this-> pullRetryLimit = Checks::checkParam($params, "pullRetryLimit");
        $this-> senderCityCode = Checks::checkParam($params, "senderCityCode");
        $this-> senderFinalName = Checks::checkParam($params, "senderFinalName");
        $this-> senderFinalTaxId = Checks::checkParam($params, "senderFinalTaxId");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> status = Checks::checkParam($params, "status");
        $this-> flow = Checks::checkParam($params, "flow");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create PixPullSubscriptions

    Send an array of PixPullSubscription objects for creation in the Stark Infra API

    ## Parameters (required):
        - subscriptions [array of PixPullSubscription objects]: array of PixPullSubscription objects to be created.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Not necessary if StarkInfra\Settings::setUser() was used.

    ## Return:
        - array of PixPullSubscription objects with updated attributes
     */
    public static function create($subscriptions, $user = null)
    {
        return Rest::post($user, PixPullSubscription::resource(), $subscriptions);
    }

    /**
    # Retrieve a specific PixPullSubscription

    Receive a single PixPullSubscription object by its id.

    ## Return:
        - PixPullSubscription object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, PixPullSubscription::resource(), $id);
    }

    /**
    # Retrieve PixPullSubscriptions

    Receive an enumerator of PixPullSubscription objects.

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. ex: 35
        - after [Date or string, default null]: date filter for objects created only after specified date. ex: "2026-04-03"
        - before [Date or string, default null]: date filter for objects created only before specified date. ex: "2026-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "active", "canceled"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["test"]
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656"]
        - user [Organization/Project object, default null]: Not necessary if StarkInfra\Settings::setUser() was used.

    ## Return:
        - enumerator of PixPullSubscription objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, PixPullSubscription::resource(), $options);
    }

    /**
    # Retrieve paged PixPullSubscriptions

    Receive a list of up to 100 PixPullSubscription objects and a cursor for the next page.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. ex: 50
        - after [Date or string, default null]: date filter for objects created only after specified date. ex: "2026-04-03"
        - before [Date or string, default null]: date filter for objects created only before specified date. ex: "2026-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "active", "canceled"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["test"]
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656"]
        - user [Organization/Project object, default null]: Not necessary if StarkInfra\Settings::setUser() was used.

    ## Return:
        - list of PixPullSubscription objects
        - cursor for the next page
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, PixPullSubscription::resource(), $options);
    }

    /**
    # Update PixPullSubscription entity

    Update mutable parameters by passing the id. When patching `status` to "confirmed",
    `senderCityCode` MUST be present in the patch.

    ## Parameters (required):
        - id [string]: PixPullSubscription unique id.

    ## Parameters (optional):
        - params [array]: associative array of fields to patch. Allowed: status, senderCityCode, reason, amount, amountMinLimit, due, pullRetryLimit, tags.
        - user [Organization/Project object, default null]: Not necessary if StarkInfra\Settings::setUser() was used.

    ## Return:
        - PixPullSubscription with updated attributes
     */
    public static function update($id, $params = [], $user = null)
    {
        return Rest::patchId($user, PixPullSubscription::resource(), $id, $params);
    }

    /**
    # Cancel a PixPullSubscription entity

    Cancel a PixPullSubscription with `reason` sent as a query parameter on the DELETE request,
    via the `deleteRaw` helper (the typed `deleteId` relay does not forward query parameters).

    ## Parameters (required):
        - id [string]: object unique id.
        - reason [string]: cancellation reason. See contract for accepted values per role.

    ## Return:
        - canceled PixPullSubscription object
     */
    public static function cancel($id, $reason, $user = null)
    {
        $resource = PixPullSubscription::resource();
        $path = "/" . API::endpoint($resource["name"]) . "/" . $id;
        $response = Rest::deleteRaw($user, $path, null, null, true, ["reason" => $reason]);
        $json = $response->json();
        $entity = $json[API::lastName($resource["name"])];
        return API::fromApiJson($resource["maker"], $entity);
    }

    /**
    # Create a single verified PixPullSubscription object from a content string

    If the provided digital signature does not check out with the Stark public key, a
    StarkInfra\Error\InvalidSignatureError will be raised.

    ## Return:
        - Parsed PixPullSubscription object
     */
    public static function parse($content, $signature, $user = null)
    {
        return Parse::parseAndVerify($content, $signature, PixPullSubscription::resource(), $user);
    }

    private static function resource()
    {
        $subscription = function ($array) {
            return new PixPullSubscription($array);
        };
        return [
            "name" => "PixPullSubscription",
            "maker" => $subscription,
        ];
    }
}
