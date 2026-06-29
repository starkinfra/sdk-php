<?php

namespace StarkInfra;
use StarkCore\Utils\API;
use StarkInfra\Utils\Rest;
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
        - bacenId [string]: Central Bank's unique recurrency id. ex: "RR20170329000000000000000003"
        - externalId [string]: safe string unique among all your subscriptions. ex: "my-external-id-1234"
        - installmentStart [string]: start datetime of settlements. ISO 8601. ex: "2026-04-01T12:00:00+00:00"
        - interval [string]: cycle definition. Options: "week", "month", "quarter", "semester", "year".
        - receiverName [string]: receiver's full name. ex: "Edward Stark"
        - receiverTaxId [string]: receiver's tax ID (CPF or CNPJ). ex: "20.018.183/0001-80"
        - senderAccountNumber [string]: sender's bank account number. ex: "00000-0"
        - senderBankCode [string]: sender's bank institution code. ex: "20018183"
        - senderBranchCode [string]: sender's bank account branch code. ex: "0001"
        - senderTaxId [string]: sender's tax ID (CPF or CNPJ). ex: "01234567890"
        - type [string]: subscription journey type. Options: "push", "qrcode", "qrcodeAndPayment", "paymentAndOrQrcode".

    ## Parameters (conditionally required):
        - amount [integer, default null]: amount in cents charged every cycle. Required if the subscription has a fixed amount. ex: 1234 (= R$ 12.34)
        - amountMinLimit [integer, default null]: floor value for variable-amount subscriptions. Required if the subscription has a variable amount. ex: 1000 (= R$ 10.00)

    ## Parameters (optional):
        - description [string, default null]: additional information delivered to the sender. ex: "Monthly subscription"
        - due [string, default null]: due date for the sender's answer. ISO 8601. ex: "2026-04-03T12:00:00+00:00"
        - installmentEnd [string, default null]: end datetime of settlements. ISO 8601. ex: "2026-12-01T12:00:00+00:00"
        - receiverBankCode [string, default null]: receiver's bank institution code. ex: "20018183"
        - referenceCode [string, default null]: commercial-relation identifier. ex: "ref-1234"
        - pullRetryLimit [integer, default null]: max retries per failed pull cycle. ex: 3
        - senderCityCode [string, default null]: IBGE code. Required if you are confirming the subscription. ex: "3550308"
        - senderFinalName [string, default null]: final sender name. ex: "Edward Stark"
        - senderFinalTaxId [string, default null]: final sender tax ID (CPF or CNPJ). ex: "01234567890"
        - tags [array of strings, default null]: list of strings for reference. ex: ["car", "house"]

    ## Attributes (return-only):
        - id [string]: unique id assigned on create. ex: "5656565656565656"
        - status [string]: current lifecycle state. Options: "created", "pending", "failed", "denied", "approved", "active", "expired", "canceled".
        - flow [string]: direction of money flow. Options: "in", "out".
        - created [DateTime]: creation datetime. ex: "2026-04-03T12:00:00+00:00"
        - updated [DateTime]: latest update datetime. ex: "2026-04-03T12:00:00+00:00"
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
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

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

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

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
        - status [array of strings, default null]: filter for status of retrieved objects. Options: "created", "pending", "failed", "denied", "approved", "active", "expired", "canceled". ex: ["active", "approved"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["test"]
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

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
        - status [array of strings, default null]: filter for status of retrieved objects. Options: "created", "pending", "failed", "denied", "approved", "active", "expired", "canceled". ex: ["active", "approved"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["test"]
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

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

    Update mutable parameters by passing the id.

    ## Parameters (required):
        - id [string]: PixPullSubscription unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - params [array]: associative array of fields to patch. Allowed keys:
            - status [string]: target status. ex: "approved"
            - senderCityCode [string]: IBGE code. Required if you are confirming the subscription. ex: "3550308"
            - reason [string]: reason for the update. Options: "accountClosed", "accountBlocked", "invalidBranchCode", "notRecognizedBySender", "userRejected", "notOffered".
            - amount [integer]: amount in cents charged every cycle. ex: 1234 (= R$ 12.34)
            - amountMinLimit [integer]: floor value for variable-amount subscriptions. ex: 1000 (= R$ 10.00)
            - due [string]: due date for the sender's answer. ISO 8601. ex: "2026-04-03T12:00:00+00:00"
            - pullRetryLimit [integer]: max retries per failed pull cycle. ex: 3
            - tags [array of strings]: list of strings for reference. ex: ["car", "house"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - PixPullSubscription with updated attributes
     */
    public static function update($id, $params = [], $user = null)
    {
        return Rest::patchId($user, PixPullSubscription::resource(), $id, $params);
    }

    /**
    # Cancel a PixPullSubscription entity

    Cancel a PixPullSubscription by passing its id and the cancellation reason.

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"
        - reason [string]: cancellation reason.
            Options as receiver: "accountClosed", "receiverOrganizationClosed", "receiverInternalError", "fraud", "receiverUserRequested".
            Options as sender: "accountClosed", "senderDeceased", "fraud", "senderUserRequested".

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

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
