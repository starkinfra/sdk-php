<?php

namespace StarkInfra;
use StarkCore\Utils\API;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;
use StarkInfra\BusinessAccountRequest\Owner;
use StarkInfra\BusinessAccountRequest\Address;


class BusinessAccountRequest extends Resource
{

    public $name;
    public $taxId;
    public $address;
    public $revenue;
    public $owners;
    public $tags;
    public $accountType;
    public $flags;
    public $status;
    public $created;
    public $updated;

    /**
    # BusinessAccountRequest object

    Request to open a Stark Infra account for a company. Each of the company's owners
    completes an identity verification through an independent webview, delivered as
    the owner's validatorLink. The API runs the approval flow asynchronously, moving
    the request through "created" -> "processing" -> ("approved" | "denied"); "failed" when the request could not be processed.

    When you initialize a BusinessAccountRequest, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the array of created objects.

    ## Parameters (required):
        - name [string]: company's legal name (minimum 5 characters). ex: "Stark Bank S.A."
        - taxId [string]: company's tax ID (CNPJ). ex: "20.018.183/0001-80"
        - address [BusinessAccountRequest\Address object or array]: company's structured address. ex: new Address(["street" => "Av. Faria Lima", "number" => "2000", "neighborhood" => "Itaim Bibi", "city" => "Sao Paulo", "state" => "SP", "zipCode" => "04538-132"])
        - revenue [integer]: company's annual revenue in cents. ex: 100000000 (= R$ 1,000,000.00)
        - owners [array of BusinessAccountRequest\Owner objects or arrays]: list of 1 to 10 company owners. ex: [new Owner(["taxId" => "012.345.678-90", "name" => "Jamie Lannister", "role" => "partner"])]

    ## Parameters (optional):
        - tags [array of strings, default null]: list of strings for reference when searching for BusinessAccountRequests. ex: ["employees", "monthly"]

    ## Attributes (return-only):
        - id [string]: unique id returned when the BusinessAccountRequest is created. ex: "5656565656565656"
        - accountType [string]: type of the account. ex: "business"
        - flags [array of dictionaries]: flags that motivated the decision, populated when the request is denied. Each flag has a code and a message. ex: [["code" => "failedIdentityProof", "message" => "O representante: 012.345.678-90 falhou na verificação de identidade."]]
        - status [string]: current status of the BusinessAccountRequest. Options: "created", "processing", "approved", "denied", "failed"
        - created [DateTime]: creation datetime for the BusinessAccountRequest.
        - updated [DateTime]: latest update datetime for the BusinessAccountRequest.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->name = Checks::checkParam($params, "name");
        $this->taxId = Checks::checkParam($params, "taxId");
        $this->address = BusinessAccountRequest::parseAddress(Checks::checkParam($params, "address"));
        $this->revenue = Checks::checkParam($params, "revenue");
        $this->owners = BusinessAccountRequest::parseOwners(Checks::checkParam($params, "owners"));
        $this->tags = Checks::checkParam($params, "tags");
        $this->accountType = Checks::checkParam($params, "accountType");
        $this->flags = Checks::checkParam($params, "flags");
        $this->status = Checks::checkParam($params, "status");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    public static function parseAddress($address)
    {
        if (is_null($address) || $address instanceof Address) {
            return $address;
        }
        $addressMaker = function ($array) {
            return new Address($array);
        };
        return API::fromApiJson($addressMaker, $address);
    }

    public static function parseOwners($owners)
    {
        if (is_null($owners)) {
            return null;
        }
        $parsedOwners = [];
        foreach ($owners as $owner) {
            if ($owner instanceof Owner) {
                array_push($parsedOwners, $owner);
                continue;
            }
            $ownerMaker = function ($array) {
                return new Owner($array);
            };
            array_push($parsedOwners, API::fromApiJson($ownerMaker, $owner));
        }
        return $parsedOwners;
    }

    /**
    # Create BusinessAccountRequests

    Send an array of BusinessAccountRequest objects for creation in the Stark Infra API

    ## Parameters (required):
        - requests [array of BusinessAccountRequest objects]: array of BusinessAccountRequest objects to be created in the API.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of BusinessAccountRequest objects with updated attributes
     */
    public static function create($requests, $user = null)
    {
        return Rest::post($user, BusinessAccountRequest::resource(), $requests);
    }

    /**
    # Retrieve a specific BusinessAccountRequest

    Receive a single BusinessAccountRequest object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - BusinessAccountRequest object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, BusinessAccountRequest::resource(), $id);
    }

    /**
    # Retrieve BusinessAccountRequests

    Receive an enumerator of BusinessAccountRequest objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null]: date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string or array of strings, default null]: filter for status of retrieved objects. Options: "created", "processing", "approved", "denied", "failed"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of BusinessAccountRequest objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, BusinessAccountRequest::resource(), $options);
    }

    /**
    # Retrieve paged BusinessAccountRequests

    Receive a list of up to 100 BusinessAccountRequest objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null]: date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string or array of strings, default null]: filter for status of retrieved objects. Options: "created", "processing", "approved", "denied", "failed"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of BusinessAccountRequest objects with updated attributes
        - cursor to retrieve the next page of BusinessAccountRequest objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, BusinessAccountRequest::resource(), $options);
    }

    private static function resource()
    {
        $request = function ($array) {
            return new BusinessAccountRequest($array);
        };
        return [
            "name" => "BusinessAccountRequest",
            "maker" => $request,
        ];
    }
}
