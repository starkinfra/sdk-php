<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class IndividualAccountAttachment extends Resource
{

    public $type;
    public $content;
    public $contentType;
    public $accountRequestId;
    public $tags;
    public $status;
    public $created;

    /**
    # IndividualAccountAttachment object

    Supporting document (identity, driver's license, selfie) attached to an
    IndividualAccountRequest for the account-approval flow. The caller uploads the
    raw image bytes and a MIME content type; the SDK encodes them as a `data:` URL
    before sending. This resource replaces the previous AccountRequestAttachment —
    the route and the SDK identifier both move from `account-request-attachment`
    to `individual-account-attachment`.

    When you initialize an IndividualAccountAttachment, the entity will not be
    automatically created in the Stark Infra API. The 'create' function sends the
    objects to the Stark Infra API and returns the array of created objects.

    ## Parameters (required):
        - type [string]: type of the IndividualAccountAttachment. Options: "drivers-license-front", "drivers-license-back", "identity-front", "identity-back", "selfie"
        - content [string]: raw image bytes at constructor time. After client-side encoding inside `create`, becomes a `data:<contentType>;base64,<payload>` URL on the wire.
        - contentType [string]: content MIME type. This parameter is required as input only — consumed client-side to build the data URL; never sent as its own wire field. ex: "image/png", "image/jpeg"
        - accountRequestId [string]: id of the parent IndividualAccountRequest. ex: "5189530608992256"

    ## Parameters (optional):
        - tags [array of strings, default null]: list of strings for reference when searching for IndividualAccountAttachments. ex: ["employees", "monthly"]

    ## Attributes (return-only):
        - id [string]: unique id returned when the IndividualAccountAttachment is created. ex: "5656565656565656"
        - status [string]: current IndividualAccountAttachment status. Options: "created", "success", "failed", "deleted"
        - created [DateTime]: creation datetime for the IndividualAccountAttachment.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->type = Checks::checkParam($params, "type");
        $this->content = Checks::checkParam($params, "content");
        $this->contentType = Checks::checkParam($params, "contentType");
        $this->accountRequestId = Checks::checkParam($params, "accountRequestId");
        $this->tags = Checks::checkParam($params, "tags");
        $this->status = Checks::checkParam($params, "status");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));

        Checks::checkParams($params);
    }

    /**
    # Create IndividualAccountAttachments

    Send an array of IndividualAccountAttachment objects for creation in the Stark Infra API.
    Each attachment's raw `content` is encoded into a `data:<contentType>;base64,<payload>`
    URL client-side before posting; `contentType` is consumed by this encoding step and
    null'd out so it is not sent as its own wire field.

    ## Parameters (required):
        - attachments [array of IndividualAccountAttachment objects]: array of IndividualAccountAttachment objects to be created in the API.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of IndividualAccountAttachment objects with updated attributes
     */
    public static function create($attachments, $user = null)
    {
        foreach ($attachments as $attachment) {
            if ($attachment->contentType != null){
                $attachment->content = "data:" . $attachment->contentType . ";base64," . base64_encode($attachment->content);
                $attachment->contentType = null;
            }
        }
        return Rest::post($user, IndividualAccountAttachment::resource(), $attachments);
    }

    /**
    # Retrieve a specific IndividualAccountAttachment

    Receive a single IndividualAccountAttachment object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - IndividualAccountAttachment object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, IndividualAccountAttachment::resource(), $id);
    }

    /**
    # Retrieve IndividualAccountAttachments

    Receive an enumerator of IndividualAccountAttachment objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. Options: "created", "success", "failed", "deleted"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of IndividualAccountAttachment objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, IndividualAccountAttachment::resource(), $options);
    }

    /**
    # Retrieve paged IndividualAccountAttachments

    Receive a list of up to 100 IndividualAccountAttachment objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null]: date filter for objects created or updated only after specified date. ex: "2020-04-03"
        - before [Date or string, default null]: date filter for objects created or updated only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. Options: "created", "success", "failed", "deleted"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of IndividualAccountAttachment objects with updated attributes
        - cursor to retrieve the next page of IndividualAccountAttachment objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, IndividualAccountAttachment::resource(), $options);
    }

    /**
    # Cancel an IndividualAccountAttachment entity

    Cancel an IndividualAccountAttachment entity previously created in the Stark Infra API.
    Maps to `DELETE /individual-account-attachment/{id}` and returns the deleted
    resource (with `status = deleted`). This is new functionality — the legacy
    AccountRequestAttachment did not expose delete.

    ## Parameters (required):
        - id [string]: IndividualAccountAttachment unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - deleted IndividualAccountAttachment object
     */
    public static function cancel($id, $user = null)
    {
        return Rest::deleteId($user, IndividualAccountAttachment::resource(), $id);
    }

    private static function resource()
    {
        $attachment = function ($array) {
            return new IndividualAccountAttachment($array);
        };
        return [
            "name" => "IndividualAccountAttachment",
            "maker" => $attachment,
        ];
    }
}
