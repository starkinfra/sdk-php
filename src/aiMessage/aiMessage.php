<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\API;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class AiMessage extends Resource
{

    public $chatId;
    public $text;
    public $model;
    public $sender;
    public $speech;
    public $metadata;
    public $chatName;
    public $created;

    /**
    # AiMessage object

    An AiMessage is a single turn of an AiChat. You post what the user said and the same call returns the user's
    message and the agent's answer.
    When you initialize an AiMessage, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the object
    to the Stark Infra API and returns the user's message and the agent's answer.

    ## Parameters (required):
        - chatId [string]: id of the AiChat to post to. ex: "5656565656565656"
        - text [string]: content of the user's message. Between 1 and 50000 characters. ex: "What is the status of my order?"

    ## Parameters (optional):
        - model [string, default null]: AI model to use for this turn only. Options: "bender-1.0", "prime-1.0". The API defaults to the agent's own model.

    ## Attributes (return-only):
        - id [string]: unique id of the AiMessage. ex: "5656565656565656"
        - sender [string]: who wrote the message. Options: "user", "system". The agent's answers are sent by "system".
        - speech [string]: version of the text written to be heard rather than read, ready to be sent to AiSpeech. Only filled when the agent has a voice.
        - metadata [array]: structured data the agent extracted, shaped by the agent's metadataSchema. The keys are the agent's, exactly as it declared them.
        - chatName [string]: title of the chat. Only present when create is called with expand=["chatName"].
        - created [DateTime]: creation datetime for the AiMessage.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->chatId = Checks::checkParam($params, "chatId");
        $this->text = Checks::checkParam($params, "text");
        $this->model = Checks::checkParam($params, "model");
        $this->sender = Checks::checkParam($params, "sender");
        $this->speech = Checks::checkParam($params, "speech");
        $this->metadata = Checks::checkParam($params, "metadata");
        $this->chatName = Checks::checkParam($params, "chatName");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));

        Checks::checkParams($params);
    }

    /**
    # Create an AiMessage object

    Post the user's message to an AiChat. The call waits for the agent, which takes a few seconds, and returns both messages.

    ## Parameters (required):
        - message [AiMessage object]: AiMessage object with chatId and text, to be created in the API

    ## Parameters (optional):
        - expand [array of strings, default null]: extra attributes to compute. Options: "chatName", which returns the chat title on every message, useful on the first turn, when the title is generated.
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array with the user's AiMessage and the agent's AiMessage
     */
    public static function create($message, $expand = null, $user = null)
    {
        $resource = AiMessage::resource();
        $payload = [
            "chatId" => $message->chatId,
            "text" => $message->text,
            "model" => $message->model
        ];
        $json = Rest::postRaw($user, API::endpoint($resource["name"]), $payload, null, true, ["expand" => $expand])->json();
        $chatName = Checks::checkParam($json, "chatName");
        return array_map(function ($entity) use ($resource, $chatName) {
            $entity["chatName"] = $chatName;
            return API::fromApiJson($resource["maker"], $entity);
        }, $json["messages"]);
    }

    /**
    # Retrieve AiMessages

    Receive an enumerator of AiMessage objects, following the cursor until the history ends.
    Without a chatId it reads the history of the whole workspace.

    ## Parameters (optional):
        - chatId [string, default null]: id of the AiChat whose messages you want. ex: "5656565656565656"
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of AiMessage objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        return Rest::getList($user, AiMessage::resource(), $options);
    }

    /**
    # Retrieve paged AiMessages

    Receive a list of up to 100 AiMessage objects and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - chatId [string, default null]: id of the AiChat whose messages you want. Without it, the history of the whole workspace. ex: "5656565656565656"
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 35
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of AiMessage objects with updated attributes
        - cursor to retrieve the next page of AiMessage objects, null on the last page
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, AiMessage::resource(), $options);
    }

    private static function resource()
    {
        $message = function ($array) {
            return new AiMessage($array);
        };
        return [
            "name" => "AiMessage",
            "maker" => $message,
        ];
    }
}
