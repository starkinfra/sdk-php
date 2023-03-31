<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class IndividualDocument extends Resource
{

    public $type;
    public $content;
    public $contentType;
    public $identityId;
    public $tags;
    public $status;
    public $created;

    /**
    # IndividualDocument object

    Individual documents are images containing either side of a document or a selfie
    to be used in a matching validation. When created, they must be attached to an individual
    identity to be used for its validation.

    When you initialize an IndividualDocument, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the array of created objects.

    ## Parameters (required):
        - type [integer]: type of the IndividualDocument. Options: "drivers-license-front", "drivers-license-back", "identity-front", "identity-back" or "selfie"
        - content [string]: Base64 data url of the picture. ex: data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAASABIAAD...
        - contentType [string]: content MIME type. This parameter is required as input only. ex: "image/png" or "image/jpeg"
        - identityId [string]: unique id of IndividualIdentity. ex: "5656565656565656"

    ## Parameters (optional):
        - tags [array of strings, default null]: list of strings for reference when searching for IndividualDocuments. ex: ["employees", "monthly"]

    ## Attributes (return-only):
        - id [string]: unique id returned when the IndividualDocument is created. ex: "5656565656565656"
        - status [string]: current IndividualDocument status. Options: "created", "canceled", "processing", "failed", "success"
        - created [DateTime]: creation datetime for the IndividualDocument. 
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> type = Checks::checkParam($params, "type");
        $this-> content = Checks::checkParam($params, "content");
        $this-> contentType = Checks::checkParam($params, "contentType");
        $this-> identityId = Checks::checkParam($params, "identityId");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> status = Checks::checkParam($params, "status");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));

        Checks::checkParams($params);
    }

    /**
    # Create IndividualDocuments

    Send an array of IndividualDocument objects for creation in the Stark Infra API

    ## Parameters (required):
        - documents [array of IndividualDocument objects]: array of IndividualDocument objects to be created in the API.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of IndividualDocument objects with updated attributes
     */
    public static function create($documents, $user = null)
    {
        foreach ($documents as $document) {
            if ($document->contentType != null){
                $document->content = "data:" . $document->contentType . ";base64," . base64_encode($document->content);
                $document->contentType = null;
            }
        }
        return Rest::post($user, IndividualDocument::resource(), $documents);
    }

    /**
    # Retrieve a specific IndividualDocument

    Receive a single IndividualDocument object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IndividualDocument object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IndividualDocument::resource(), $id);
    }

    /**
    # Retrieve IndividualDocuments

    Receive an enumerator of IndividualDocument objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. Options: "created", "canceled", "processing", "failed" and "success"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IndividualDocument objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, IndividualDocument::resource(), $options);
    }

    /**
    # Retrieve paged IndividualDocuments

    Receive a list of up to 100 IndividualDocument objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. Options: "created", "canceled", "processing", "failed" and "success"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of IndividualDocument objects with updated attributes
        - cursor to retrieve the next page of IndividualDocument objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, IndividualDocument::resource(), $options);
    }

    private static function resource()
    {
        $document = function ($array) {
            return new IndividualDocument($array);
        };
        return [
            "name" => "IndividualDocument",
            "maker" => $document,
        ];
    }
}
