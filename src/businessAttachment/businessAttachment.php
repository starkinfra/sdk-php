<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class BusinessAttachment extends Resource
{

    public $name;
    public $content;
    public $contentType;
    public $businessIdentityId;
    public $tags;
    public $attachmentId;
    public $status;
    public $created;
    public $updated;

    /**
    # BusinessAttachment object

    Business attachments are files containing documents of a legal entity to be used
    in a matching validation. When created, they must be attached to a business
    identity to be used for its validation.

    When you initialize a BusinessAttachment, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the array of created objects.

    ## Parameters (required):
        - name [string]: name of the BusinessAttachment. ex: "articles-of-incorporation.pdf"
        - content [string]: Base64 data url of the file or raw bytes. ex: data:application/pdf;base64,JVBERi0xLjQKJ...
        - businessIdentityId [string]: unique id of BusinessIdentity. ex: "5656565656565656"

    ## Parameters (optional):
        - contentType [string, default null]: content MIME type. This parameter is required as input only. ex: "image/png" or "image/jpeg"
        - tags [array of strings, default null]: array of strings for reference when searching for BusinessAttachments. ex: ["employees", "monthly"]

    ## Attributes (return-only):
        - id [string]: unique id returned when the BusinessAttachment is created. ex: "5656565656565656"
        - attachmentId [string]: unique id of the attached file. ex: "5656565656565656"
        - status [string]: current BusinessAttachment status. Options: "created", "canceled", "approved", "denied"
        - created [DateTime]: creation datetime for the BusinessAttachment.
        - updated [DateTime]: latest update datetime for the BusinessAttachment.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> name = Checks::checkParam($params, "name");
        $this-> content = Checks::checkParam($params, "content");
        $this-> contentType = Checks::checkParam($params, "contentType");
        $this-> businessIdentityId = Checks::checkParam($params, "businessIdentityId");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> attachmentId = Checks::checkParam($params, "attachmentId");
        $this-> status = Checks::checkParam($params, "status");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create BusinessAttachments

    Send an array of BusinessAttachment objects for creation in the Stark Infra API

    ## Parameters (required):
        - attachments [array of BusinessAttachment objects]: array of BusinessAttachment objects to be created in the API.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of BusinessAttachment objects with updated attributes
     */
    public static function create($attachments, $user = null)
    {
        foreach ($attachments as $attachment) {
            if ($attachment->contentType != null){
                $attachment->content = "data:" . $attachment->contentType . ";base64," . base64_encode($attachment->content);
                $attachment->contentType = null;
            }
        }
        return Rest::post($user, BusinessAttachment::resource(), $attachments);
    }

    /**
    # Retrieve a specific BusinessAttachment

    Receive a single BusinessAttachment object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - expand [array of strings, default null]: fields to expand information. ex: ["content"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - BusinessAttachment object with updated attributes
     */
    public static function get($id, $options = [], $user = null)
    {
        return Rest::getId($user, BusinessAttachment::resource(), $id, $options);
    }

    /**
    # Retrieve BusinessAttachments

    Receive an enumerator of BusinessAttachment objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. Options: "created", "canceled", "approved" and "denied"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of BusinessAttachment objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, BusinessAttachment::resource(), $options);
    }

    /**
    # Retrieve paged BusinessAttachments

    Receive a list of up to 100 BusinessAttachment objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. Options: "created", "canceled", "approved" and "denied"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of BusinessAttachment objects with updated attributes
        - cursor to retrieve the next page of BusinessAttachment objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, BusinessAttachment::resource(), $options);
    }

    /**
    # Cancel a BusinessAttachment entity

    Cancel a BusinessAttachment entity previously created in the Stark Infra API

    ## Parameters (required):
        - id [string]: BusinessAttachment unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - canceled BusinessAttachment object
     */
    public static function cancel($id, $user = null)
    {
        return Rest::deleteId($user, BusinessAttachment::resource(), $id);
    }

    private static function resource()
    {
        $attachment = function ($array) {
            return new BusinessAttachment($array);
        };
        return [
            "name" => "BusinessAttachment",
            "maker" => $attachment,
        ];
    }
}
