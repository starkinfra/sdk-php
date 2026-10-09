<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\API;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class AiAgent extends Resource
{

    public $name;
    public $model;
    public $systemPrompt;
    public $voiceId;
    public $knowledgeBaseIds;
    public $metadataSchema;
    public $knowledgeBases;
    public $created;
    public $updated;

    /**
    # AiAgent object

    An AiAgent is the configuration of an assistant: the model, the instructions, the knowledge it may consult and
    the voice it speaks with. The agent never changes during a conversation; the conversation lives in an AiChat
    and each turn is an AiMessage.
    When you initialize an AiAgent, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the object
    to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - name [string]: name of the agent. Between 1 and 100 characters. ex: "Support assistant"
        - model [string]: AI model the agent runs on. Options: "bender-1.0" for everyday conversations, "prime-1.0" for harder reasoning.

    ## Parameters (optional):
        - systemPrompt [string, default null]: instructions that define the agent's persona, tone and domain behavior. Up to 100000 characters. The API falls back to its default assistant prompt when omitted.
        - voiceId [string, default null]: id of the AiVoice the agent speaks with. When set, every reply also carries a speech string ready to be sent to AiSpeech. The API does not check that the voice exists.
        - knowledgeBaseIds [array of strings, default null]: ids of up to 100 AiKnowledgeBases the agent retrieves from before answering. The API does not check that they exist.
        - metadataSchema [array, default null]: flat array whose keys are the fields the agent must extract on every reply. Each field takes a "type" (string, integer, number, boolean or array), an optional "description" of up to 2000 characters, an optional "enum" of up to 20 strings for string fields. The keys are yours and are sent exactly as written. ex: ["order_id" => ["type" => "string", "description" => "Order the customer mentions"]]

    ## Attributes (return-only):
        - id [string]: unique id returned when the AiAgent is created. ex: "5656565656565656"
        - knowledgeBases [array of AiKnowledgeBase objects]: the knowledge bases themselves. Only present when requested with expand=["knowledgeBases"].
        - created [DateTime]: creation datetime for the AiAgent.
        - updated [DateTime]: latest update datetime for the AiAgent.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->name = Checks::checkParam($params, "name");
        $this->model = Checks::checkParam($params, "model");
        $this->systemPrompt = Checks::checkParam($params, "systemPrompt");
        $this->voiceId = Checks::checkParam($params, "voiceId");
        $this->knowledgeBaseIds = Checks::checkParam($params, "knowledgeBaseIds");
        $this->metadataSchema = Checks::checkParam($params, "metadataSchema");
        $this->knowledgeBases = AiAgent::parseKnowledgeBases(Checks::checkParam($params, "knowledgeBases"));
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create an AiAgent object

    Send an AiAgent object for creation at the Stark Infra API

    ## Parameters (required):
        - agent [AiAgent object]: AiAgent object to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - AiAgent object with updated attributes
     */
    public static function create($agent, $user = null)
    {
        $payload = [
            "name" => $agent->name,
            "model" => $agent->model,
            "systemPrompt" => $agent->systemPrompt,
            "voiceId" => $agent->voiceId,
            "knowledgeBaseIds" => $agent->knowledgeBaseIds,
            "metadataSchema" => $agent->metadataSchema === [] ? (object) [] : $agent->metadataSchema
        ];
        $resource = AiAgent::resource();
        $json = Rest::postRaw($user, API::endpoint($resource["name"]), $payload)->json();
        return API::fromApiJson($resource["maker"], $json[API::lastName($resource["name"])]);
    }

    /**
    # Retrieve a specific AiAgent

    Receive a single AiAgent object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - expand [array of strings, default null]: extra attributes to compute. Options: "knowledgeBases".
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - AiAgent object with updated attributes
     */
    public static function get($id, $options = [], $user = null)
    {
        return Rest::getId($user, AiAgent::resource(), $id, $options);
    }

    /**
    # Retrieve AiAgents

    Receive an enumerator of AiAgent objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - expand [array of strings, default null]: extra attributes to compute. Options: "knowledgeBases".
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of AiAgent objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        return Rest::getList($user, AiAgent::resource(), $options);
    }

    /**
    # Retrieve paged AiAgents

    Receive a list of up to 100 AiAgent objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - expand [array of strings, default null]: extra attributes to compute. Options: "knowledgeBases".
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of AiAgent objects with updated attributes
        - cursor to retrieve the next page of AiAgent objects, null on the last page
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, AiAgent::resource(), $options);
    }

    /**
    # Update AiAgent entity

    Update an AiAgent's parameters by passing its id. The API keeps what you do not send;
    clear systemPrompt and voiceId with "", knowledgeBaseIds with [] and metadataSchema with an empty array (sent as {}).

    ## Parameters (required):
        - id [string]: AiAgent unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - name [string, default null]: new name for the agent. Between 1 and 100 characters.
        - model [string, default null]: new AI model. Options: "bender-1.0", "prime-1.0"
        - systemPrompt [string, default null]: new instructions for the agent. Up to 100000 characters.
        - voiceId [string, default null]: new AiVoice id.
        - knowledgeBaseIds [array of strings, default null]: the AiKnowledgeBase ids the agent should end up with. Replaces the current list.
        - metadataSchema [array, default null]: new schema of the structured data the agent must extract. The keys are yours and are sent exactly as written.
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - target AiAgent with updated attributes
     */
    public static function update($id, $options = [], $user = null)
    {
        $metadataSchema = Checks::checkParam($options, "metadataSchema");
        $payload = [
            "name" => Checks::checkParam($options, "name"),
            "model" => Checks::checkParam($options, "model"),
            "systemPrompt" => Checks::checkParam($options, "systemPrompt"),
            "voiceId" => Checks::checkParam($options, "voiceId"),
            "knowledgeBaseIds" => Checks::checkParam($options, "knowledgeBaseIds"),
            "metadataSchema" => $metadataSchema === [] ? (object) [] : $metadataSchema
        ];
        $resource = AiAgent::resource();
        $json = Rest::patchRaw($user, API::endpoint($resource["name"]) . "/" . Checks::checkId($id), $payload)->json();
        return API::fromApiJson($resource["maker"], $json[API::lastName($resource["name"])]);
    }

    /**
    # Delete AiAgents

    Delete up to 100 AiAgents at once.

    ## Parameters (required):
        - ids [array of strings]: ids of the AiAgents to be deleted. Up to 100 ids. ex: ["5656565656565656", "4545454545454545"]

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of deleted AiAgent objects
     */
    public static function delete($ids, $user = null)
    {
        $resource = AiAgent::resource();
        $json = Rest::deleteRaw($user, API::endpoint($resource["name"]), null, null, true, ["ids" => $ids])->json();
        return array_map(function ($entity) use ($resource) {
            return API::fromApiJson($resource["maker"], $entity);
        }, $json[API::lastNamePlural($resource["name"])]);
    }

    private static function parseKnowledgeBases($knowledgeBases)
    {
        if (is_null($knowledgeBases)) {
            return null;
        }
        return array_map(function ($knowledgeBase) {
            if (!is_array($knowledgeBase)) {
                return $knowledgeBase;
            }
            return API::fromApiJson(function ($array) {
                return new AiKnowledgeBase($array);
            }, $knowledgeBase);
        }, $knowledgeBases);
    }

    private static function resource()
    {
        $agent = function ($array) {
            return new AiAgent($array);
        };
        return [
            "name" => "AiAgent",
            "maker" => $agent,
        ];
    }
}
