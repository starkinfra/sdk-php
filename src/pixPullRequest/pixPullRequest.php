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
        - status [string]: Options: "created", "scheduled", "active", "denied", "canceled", "failed".
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

    /**
    # Create PixPullRequests

    Send a list of PixPullRequest objects for creation in the Stark Infra API.

    ## Parameters (required):
        - requests [array of PixPullRequest objects]: PixPullRequest objects to be created.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Not necessary if StarkInfra\Settings::setUser() was used.

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
        - user [Organization/Project object, default null]: Not necessary if StarkInfra\Settings::setUser() was used.

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
        - status [string, default null]: filter for status of retrieved objects. ex: "created", "scheduled", "active", "denied", "canceled", "failed"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["test"]
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656"]
        - flow [string, default null]: direction of the request from the sender. ex: "in", "out"
        - subscriptionIds [array of strings, default null]: filter by the parent PixPullSubscription ids. ex: ["5656565656565656"]
        - user [Organization/Project object, default null]: Not necessary if StarkInfra\Settings::setUser() was used.

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
        - status [string, default null]: filter for status of retrieved objects. ex: "created", "scheduled", "active", "denied", "canceled", "failed"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["test"]
        - ids [array of strings, default null]: list of ids to filter retrieved objects. ex: ["5656565656565656"]
        - flow [string, default null]: direction of the request from the sender. ex: "in", "out"
        - subscriptionIds [array of strings, default null]: filter by the parent PixPullSubscription ids. ex: ["5656565656565656"]
        - user [Organization/Project object, default null]: Not necessary if StarkInfra\Settings::setUser() was used.

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
        - user [Organization/Project object, default null]: Not necessary if StarkInfra\Settings::setUser() was used.

    ## Return:
        - PixPullRequest object with updated attributes
     */
    public static function update($id, $params = [], $user = null)
    {
        return Rest::patchId($user, PixPullRequest::resource(), $id, $params);
    }

    /**
    # Cancel a PixPullRequest

    Cancel a PixPullRequest by passing the id. `reason` is sent as a query parameter on the
    DELETE request via `deleteRaw` (the typed `deleteId` relay does not forward query parameters).

    ## Parameters (required):
        - id [string]: PixPullRequest unique id. ex: "5656565656565656"
        - reason [string]: cancellation reason.
            Options as receiver: "accountClosed", "receiverOrganizationClosed", "receiverInternalError", "fraud", "receiverUserRequested".
            Options as sender: "accountClosed", "senderDeceased", "fraud", "senderUserRequested".

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Not necessary if StarkInfra\Settings::setUser() was used.

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
