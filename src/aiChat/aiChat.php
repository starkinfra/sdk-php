<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\API;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class AiChat extends Resource
{

    public $agentId;
    public $title;
    public $tags;
    public $context;
    public $agentName;
    public $updated;

    /**
    # AiChat object

    An AiChat is a conversation with an AiAgent. It holds the history; each turn is an AiMessage posted to it.
    When you initialize an AiChat, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the object
    to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - agentId [string]: id of the AiAgent that answers in this chat. ex: "5656565656565656"

    ## Parameters (optional):
        - title [string, default null]: title of the chat. Up to 100 characters. The API generates one from the first turn when omitted. ex: "Order 123"
        - tags [array of strings, default null]: array of strings for reference when searching for AiChats. ex: ["vip", "order"]
        - context [array, default null]: free-form data about the conversation, such as the order or the customer it is about. The keys are yours and are sent exactly as written. ex: ["orderId" => "123"]

    ## Attributes (return-only):
        - id [string]: unique id returned when the AiChat is created. ex: "5656565656565656"
        - agentName [string]: name of the AiAgent. Only present when requested with expand=["agentName"].
        - updated [DateTime]: latest update datetime for the AiChat.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->agentId = Checks::checkParam($params, "agentId");
        $this->title = Checks::checkParam($params, "title");
        $this->tags = Checks::checkParam($params, "tags");
        $this->context = Checks::checkParam($params, "context");
        $this->agentName = Checks::checkParam($params, "agentName");
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create an AiChat object

    Send an AiChat object for creation at the Stark Infra API

    ## Parameters (required):
        - chat [AiChat object]: AiChat object to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - AiChat object with updated attributes
     */
    public static function create($chat, $user = null)
    {
        $payload = [
            "agentId" => $chat->agentId,
            "title" => $chat->title,
            "tags" => $chat->tags,
            "context" => $chat->context === [] ? (object) [] : $chat->context
        ];
        $resource = AiChat::resource();
        $json = Rest::postRaw($user, API::endpoint($resource["name"]), $payload)->json();
        return API::fromApiJson($resource["maker"], $json[API::lastName($resource["name"])]);
    }

    /**
    # Retrieve a specific AiChat

    Receive a single AiChat object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - expand [array of strings, default null]: extra attributes to compute. Options: "agentName".
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - AiChat object with updated attributes
     */
    public static function get($id, $options = [], $user = null)
    {
        return Rest::getId($user, AiChat::resource(), $id, $options);
    }

    /**
    # Retrieve AiChats

    Receive an enumerator of AiChat objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - expand [array of strings, default null]: extra attributes to compute. Options: "agentName".
        - tags [array of strings, default null]: array of strings to filter retrieved objects. ex: ["vip", "order"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of AiChat objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        return Rest::getList($user, AiChat::resource(), $options);
    }

    /**
    # Retrieve paged AiChats

    Receive a list of up to 100 AiChat objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - expand [array of strings, default null]: extra attributes to compute. Options: "agentName".
        - tags [array of strings, default null]: array of strings to filter retrieved objects. ex: ["vip", "order"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of AiChat objects with updated attributes
        - cursor to retrieve the next page of AiChat objects, null on the last page
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, AiChat::resource(), $options);
    }

    /**
    # Update AiChat entity

    Rename a chat, hand it to another AiAgent, or change its tags and context. The history is kept.
    The API keeps what you do not send; clear the title with "", the tags with [] and the context with an empty array (sent as {}).

    ## Parameters (required):
        - id [string]: AiChat unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - title [string, default null]: new title of the chat. Up to 100 characters.
        - agentId [string, default null]: id of the AiAgent that answers from now on.
        - tags [array of strings, default null]: new array of strings. Replaces the current list.
        - context [array, default null]: new free-form data about the conversation. The keys are yours and are sent exactly as written.
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - target AiChat with updated attributes
     */
    public static function update($id, $options = [], $user = null)
    {
        $context = Checks::checkParam($options, "context");
        $payload = [
            "title" => Checks::checkParam($options, "title"),
            "agentId" => Checks::checkParam($options, "agentId"),
            "tags" => Checks::checkParam($options, "tags"),
            "context" => $context === [] ? (object) [] : $context
        ];
        $resource = AiChat::resource();
        $json = Rest::patchRaw($user, API::endpoint($resource["name"]) . "/" . Checks::checkId($id), $payload)->json();
        return API::fromApiJson($resource["maker"], $json[API::lastName($resource["name"])]);
    }

    /**
    # Delete AiChats

    Delete up to 100 AiChats at once, with their messages.

    ## Parameters (required):
        - ids [array of strings]: ids of the AiChats to be deleted. Up to 100 ids. ex: ["5656565656565656", "4545454545454545"]

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of deleted AiChat objects
     */
    public static function delete($ids, $user = null)
    {
        $resource = AiChat::resource();
        $json = Rest::deleteRaw($user, API::endpoint($resource["name"]), null, null, true, ["ids" => $ids])->json();
        return array_map(function ($entity) use ($resource) {
            return API::fromApiJson($resource["maker"], $entity);
        }, $json[API::lastNamePlural($resource["name"])]);
    }

    private static function resource()
    {
        $chat = function ($array) {
            return new AiChat($array);
        };
        return [
            "name" => "AiChat",
            "maker" => $chat,
        ];
    }
}
