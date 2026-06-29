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
        - amount [integer]: amount to be charged in cents. ex: 1234 (= R$ 12.34)
        - due [string]: due date for answering with an approval or denial. ISO 8601. ex: "2026-04-03T12:00:00+00:00"
        - endToEndId [string]: Central Bank's unique transaction id. ex: "E20018183202201201450u34sDGd19lz"
        - receiverAccountNumber [string]: receiver's bank account number. ex: "00000-0"
        - receiverAccountType [string]: receiver's bank account type. Options: "checking", "savings", "salary", "payment".
        - receiverBankCode [string]: receiver's bank code. ex: "20018183"
        - reconciliationId [string]: id used for conciliation of the resulting Pix transaction. ex: "b77f5236-7ab9-4487-9f95-66ee6eaf1781"
        - subscriptionId [string]: unique id of the parent PixPullSubscription. ex: "5656565656565656"

    ## Parameters (optional):
        - attemptType [string, default null]: retry behavior for the pull. Options: "default", "instantRetry", "scheduledRetry".
        - description [string, default null]: additional information delivered to the sender. ex: "Payment for service #1234"
        - receiverBranchCode [string, default null]: receiver's bank account branch code. ex: "0001"
        - tags [array of strings, default null]: list of strings for tagging. ex: ["car", "house"]

    ## Attributes (return-only):
        - id [string]: unique id returned when the PixPullRequest is created. ex: "5656565656565656"
        - status [string]: current PixPullRequest status. Options: "created", "processing", "scheduled", "denied", "success", "canceled", "expired".
        - flow [string]: direction of the money flow. Options: "in", "out".
        - receiverName [string]: receiver's full name. ex: "Anthony Edward Stark"
        - receiverTaxId [string]: receiver's tax ID (CPF or CNPJ). ex: "01234567890"
        - senderBankCode [string]: sender's bank code. ex: "20018183"
        - senderFinalName [string]: sender's final name. ex: "Edward Stark"
        - senderTaxId [string]: sender's tax ID (CPF or CNPJ). ex: "01234567890"
        - subscriptionBacenId [string]: Central Bank's unique id of the parent subscription. ex: "RR20170329000000000000000003"
        - created [DateTime]: creation datetime for the PixPullRequest. ex: "2026-04-03T12:00:00+00:00"
        - updated [DateTime]: latest update datetime for the PixPullRequest. ex: "2026-04-03T12:00:00+00:00"
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

    /**
    # Create PixPullRequests

    Send a list of PixPullRequest objects for creation in the Stark Infra API.

    ## Parameters (required):
        - requests [array of PixPullRequest objects]: PixPullRequest objects to be created.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of PixPullRequest objects with updated attributes
     */
    public static function create($requests, $user = null)
    {
        return Rest::post($user, PixPullRequest::resource(), $requests);
    }

    /**
    # Retrieve a specific PixPullRequest

    Receive a single PixPullRequest object by its id.

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - PixPullRequest object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, PixPullRequest::resource(), $id);
    }

    /**
    # Retrieve PixPullRequests

    Receive an enumerator of PixPullRequest objects.

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. ex: 35
        - after [Date or string, default null]: date filter for objects created only after specified date. ex: "2026-04-03"
        - before [Date or string, default null]: date filter for objects created only before specified date. ex: "2026-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. Options: "created", "processing", "scheduled", "denied", "success", "canceled", "expired". ex: ["created", "scheduled"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["test"]
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656"]
        - flows [array of strings, default null]: filter for the direction of the money flow. Options: "in", "out". ex: ["in", "out"]
        - subscriptionIds [array of strings, default null]: filter by the parent PixPullSubscription ids. ex: ["5656565656565656"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of PixPullRequest objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, PixPullRequest::resource(), $options);
    }

    /**
    # Retrieve paged PixPullRequests

    Receive a list of up to 100 PixPullRequest objects and a cursor for the next page.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. ex: 50
        - after [Date or string, default null]: date filter for objects created only after specified date. ex: "2026-04-03"
        - before [Date or string, default null]: date filter for objects created only before specified date. ex: "2026-04-03"
        - status [array of strings, default null]: filter for status of retrieved objects. Options: "created", "processing", "scheduled", "denied", "success", "canceled", "expired". ex: ["created", "scheduled"]
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["test"]
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656"]
        - flows [array of strings, default null]: filter for the direction of the money flow. Options: "in", "out". ex: ["in", "out"]
        - subscriptionIds [array of strings, default null]: filter by the parent PixPullSubscription ids. ex: ["5656565656565656"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of PixPullRequest objects with updated attributes
        - cursor for the next page
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, PixPullRequest::resource(), $options);
    }

    /**
    # Update PixPullRequest

    Change status to "scheduled" or "denied". When denying, `reason` is required.

    ## Parameters (required):
        - id [string]: PixPullRequest unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - params [array]: associative array of fields to patch. Allowed keys:
            - status [string]: target status. Options: "scheduled", "denied".
            - reason [string]: reason for denial. Options: "senderAccountClosed", "senderAccountBlocked", "amountNotAllowed".
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - PixPullRequest object with updated attributes
     */
    public static function update($id, $params = [], $user = null)
    {
        return Rest::patchId($user, PixPullRequest::resource(), $id, $params);
    }

    /**
    # Cancel a PixPullRequest

    Cancel a PixPullRequest by passing its id and the cancellation reason.

    ## Parameters (required):
        - id [string]: PixPullRequest unique id. ex: "5656565656565656"
        - reason [string]: cancellation reason.
            Options as receiver: "accountClosed", "receiverOrganizationClosed", "receiverInternalError", "fraud", "receiverUserRequested".
            Options as sender: "accountClosed", "senderDeceased", "fraud", "senderUserRequested".

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - canceled PixPullRequest object
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
