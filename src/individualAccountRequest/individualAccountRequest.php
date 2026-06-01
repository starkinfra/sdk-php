<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class IndividualAccountRequest extends Resource
{

    public $name;
    public $taxId;
    public $address;
    public $income;
    public $tags;
    public $status;
    public $accountType;
    public $flags;
    public $created;
    public $updated;

    /**
    # IndividualAccountRequest object

    Request to open a Stark Infra account for an individual. The caller submits the
    individual's identifying data and income, and the API runs the approval flow
    asynchronously — moving the request through "created" -> "processing" ->
    ("success" | "failed" | "canceled"). Supporting documents are uploaded as
    IndividualAccountAttachment and reference this request via accountRequestId.

    When you initialize an IndividualAccountRequest, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the array of created objects.

    ## Parameters (required):
        - name [string]: full legal name of the individual. ex: "Tony Stark"
        - taxId [string]: Brazilian CPF. ex: "012.345.678-90"
        - address [array]: structured residential address with required keys "street", "number", "neighborhood", "city", "state", "zipCode". Serialized as a nested JSON object on the wire.
        - income [integer]: monthly income in cents. Must be > 0. ex: 1000000 (R$ 10,000.00)

    ## Parameters (optional):
        - tags [array of strings, default null]: list of strings for reference when searching for IndividualAccountRequests. ex: ["employees", "monthly"]

    ## Attributes (return-only):
        - id [string]: unique id returned when the IndividualAccountRequest is created. ex: "5189530608992256"
        - status [string]: current IndividualAccountRequest status. Options: "created", "processing", "success", "failed", "canceled"
        - accountType [string]: always "individual" for this resource. Returned for parity with other account-request kinds.
        - flags [array of strings]: server-side review flags. Empty unless the request triggered a manual-review condition.
        - created [DateTime]: creation datetime for the IndividualAccountRequest.
        - updated [DateTime]: latest update datetime for the IndividualAccountRequest.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->name = Checks::checkParam($params, "name");
        $this->taxId = Checks::checkParam($params, "taxId");
        $this->address = Checks::checkParam($params, "address");
        $this->income = Checks::checkParam($params, "income");
        $this->tags = Checks::checkParam($params, "tags");
        $this->status = Checks::checkParam($params, "status");
        $this->accountType = Checks::checkParam($params, "accountType");
        $this->flags = Checks::checkParam($params, "flags");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create IndividualAccountRequests

    Send an array of IndividualAccountRequest objects for creation in the Stark Infra API

    ## Parameters (required):
        - requests [array of IndividualAccountRequest objects]: array of IndividualAccountRequest objects to be created in the API.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of IndividualAccountRequest objects with updated attributes
     */
    public static function create($requests, $user = null)
    {
        return Rest::post($user, IndividualAccountRequest::resource(), $requests);
    }

    /**
    # Retrieve a specific IndividualAccountRequest

    Receive a single IndividualAccountRequest object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5189530608992256"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IndividualAccountRequest object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IndividualAccountRequest::resource(), $id);
    }

    /**
    # Retrieve IndividualAccountRequests

    Receive an enumerator of IndividualAccountRequest objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. Options: "created", "processing", "success", "failed", "canceled"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5189530608992256", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IndividualAccountRequest objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, IndividualAccountRequest::resource(), $options);
    }

    /**
    # Retrieve paged IndividualAccountRequests

    Receive a list of up to 100 IndividualAccountRequest objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. Options: "created", "processing", "success", "failed", "canceled"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5189530608992256", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of IndividualAccountRequest objects with updated attributes
        - cursor to retrieve the next page of IndividualAccountRequest objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, IndividualAccountRequest::resource(), $options);
    }

    /**
    # Update IndividualAccountRequest entity

    Update an IndividualAccountRequest by passing id. Accepts any subset of
    name, taxId, address, income, status, tags. Replaces — does NOT deep-merge —
    the address object.

    ## Parameters (required):
        - id [string]: IndividualAccountRequest id. ex: "5189530608992256"

    ## Parameters (optional):
        - name [string, default null]: new full legal name. ex: "Tony Stark"
        - taxId [string, default null]: new Brazilian CPF.
        - address [array, default null]: replacement address object. Replaces — does not deep-merge.
        - income [integer, default null]: replacement monthly income in cents.
        - status [string, default null]: manual state transition. ex: "processing"
        - tags [array of strings, default null]: replacement tag list.
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - target IndividualAccountRequest with updated attributes
     */
    public static function update($id, $options = [], $user = null)
    {
        return Rest::patchId($user, IndividualAccountRequest::resource(), $id, $options);
    }

    private static function resource()
    {
        $request = function ($array) {
            return new IndividualAccountRequest($array);
        };
        return [
            "name" => "IndividualAccountRequest",
            "maker" => $request,
        ];
    }
}
