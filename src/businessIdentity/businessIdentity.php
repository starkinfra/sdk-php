<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class BusinessIdentity extends Resource
{

    public $taxId;
    public $tags;
    public $name;
    public $taxIdStatus;
    public $insightTaxId;
    public $insightDocumentType;
    public $numPages;
    public $representatives;
    public $attachments;
    public $rules;
    public $status;
    public $created;
    public $updated;

    /**
    # BusinessIdentity object

    A BusinessIdentity represents a legal entity to be validated. It can have several business attachments
    attached to it, which are used to validate the identity of the business. Once a business identity is created,
    business attachments must be attached to it using the created method of the business attachment resource. When all
    the required business attachments are attached to a business identity it can be sent to validation by patching its
    status to processing.

    When you initialize a BusinessIdentity, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the array of created objects.

    ## Parameters (required):
        - taxId [string]: business's tax ID (CNPJ). ex: "20.018.183/0001-80"

    ## Parameters (optional):
        - tags [array of strings, default null]: array of strings for reference when searching for BusinessIdentities. ex: ["employees", "monthly"]

    ## Attributes (return-only):
        - id [string]: unique id returned when the identity is created. ex: "5656565656565656"
        - name [string]: business's full name. ex: "Stark Bank S.A."
        - taxIdStatus [string]: current status of the tax ID. ex: "active"
        - insightTaxId [string]: tax ID extracted from the attached documents. ex: "20.018.183/0001-80"
        - insightDocumentType [string]: document type extracted from the attached documents. ex: "articles-of-incorporation"
        - numPages [integer]: number of pages extracted from the attached documents. ex: 12
        - representatives [string]: JSON string with the representatives extracted from the attached documents.
        - attachments [array of strings]: array of BusinessAttachment ids attached to the identity. ex: ["5656565656565656", "4545454545454545"]
        - rules [string]: JSON string with the validation rules of the identity.
        - status [string]: current status of the BusinessIdentity. Options: "created", "pending", "canceled", "processing", "success", "failed"
        - created [DateTime]: creation datetime for the BusinessIdentity.
        - updated [DateTime]: latest update datetime for the BusinessIdentity.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> taxId = Checks::checkParam($params, "taxId");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> name = Checks::checkParam($params, "name");
        $this-> taxIdStatus = Checks::checkParam($params, "taxIdStatus");
        $this-> insightTaxId = Checks::checkParam($params, "insightTaxId");
        $this-> insightDocumentType = Checks::checkParam($params, "insightDocumentType");
        $this-> numPages = Checks::checkParam($params, "numPages");
        $this-> representatives = Checks::checkParam($params, "representatives");
        $this-> attachments = Checks::checkParam($params, "attachments");
        $this-> rules = Checks::checkParam($params, "rules");
        $this-> status = Checks::checkParam($params, "status");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create BusinessIdentities

    Send an array of BusinessIdentity objects for creation in the Stark Infra API

    ## Parameters (required):
        - identities [array of BusinessIdentity objects]: array of BusinessIdentity objects to be created in the API.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of BusinessIdentity objects with updated attributes
     */
    public static function create($identities, $user = null)
    {
        return Rest::post($user, BusinessIdentity::resource(), $identities);
    }

    /**
    # Retrieve a specific BusinessIdentity

    Receive a single BusinessIdentity object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - BusinessIdentity object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, BusinessIdentity::resource(), $id);
    }

    /**
    # Retrieve BusinessIdentities

    Receive an enumerator of BusinessIdentity objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. Options: "created", "pending", "canceled", "processing", "success" and "failed"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - taxIds [array of strings, default null]: array of tax IDs to filter retrieved objects. ex: ["20.018.183/0001-80", "01.234.567/0001-89"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of BusinessIdentity objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, BusinessIdentity::resource(), $options);
    }

    /**
    # Retrieve paged BusinessIdentities

    Receive a list of up to 100 BusinessIdentity objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. Options: "created", "pending", "canceled", "processing", "success" and "failed"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - taxIds [array of strings, default null]: array of tax IDs to filter retrieved objects. ex: ["20.018.183/0001-80", "01.234.567/0001-89"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of BusinessIdentity objects with updated attributes
        - cursor to retrieve the next page of BusinessIdentity objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, BusinessIdentity::resource(), $options);
    }

    /**
    # Update BusinessIdentity entity

    Update a BusinessIdentity by passing id.

    ## Parameters (required):
        - id [string]: BusinessIdentity id. ex: "5656565656565656"

    ## Parameters (optional):
        - status [string, default null]: You may send BusinessAttachments to validation by passing 'processing' in the status
        - tags [array of strings, default null]: array of strings for reference when searching for BusinessIdentities. ex: ["employees", "monthly"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - target BusinessIdentity with updated attributes
     */
    public static function update($id, $options = [], $user = null)
    {
        return Rest::patchId($user, BusinessIdentity::resource(), $id, $options);
    }

    /**
    # Cancel a BusinessIdentity entity

    Cancel a BusinessIdentity entity previously created in the Stark Infra API

    ## Parameters (required):
        - id [string]: BusinessIdentity unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - canceled BusinessIdentity object
     */
    public static function cancel($id, $user = null)
    {
        return Rest::deleteId($user, BusinessIdentity::resource(), $id);
    }

    private static function resource()
    {
        $identity = function ($array) {
            return new BusinessIdentity($array);
        };
        return [
            "name" => "BusinessIdentity",
            "maker" => $identity,
        ];
    }
}
