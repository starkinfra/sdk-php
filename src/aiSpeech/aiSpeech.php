<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\API;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class AiSpeech extends Resource
{

    public $voiceId;
    public $text;
    public $status;
    public $audio;
    public $voiceName;
    public $errors;
    public $created;
    public $updated;

    /**
    # AiSpeech object

    An AiSpeech is a text read out loud by an AiVoice. The synthesis is synchronous: the object returned by
    'create' already comes back in "success" status carrying the audio.
    When you initialize an AiSpeech, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the object
    to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - voiceId [string]: id of the AiVoice that reads the text. ex: "5656565656565656"
        - text [string]: text to be read out loud. ex: "Your order has shipped."

    ## Attributes (return-only):
        - id [string]: unique id returned when the AiSpeech is created. ex: "5656565656565656"
        - status [string]: current status of the speech. Options: "processing", "success", "failed"
        - audio [string]: base64-encoded audio of the speech. Left out of query and page results.
        - voiceName [string]: name of the AiVoice. Only present when requested with expand=["voiceName"].
        - errors [array of strings]: reasons the synthesis failed. Empty while the speech is healthy.
        - created [DateTime]: creation datetime for the AiSpeech.
        - updated [DateTime]: latest update datetime for the AiSpeech.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->voiceId = Checks::checkParam($params, "voiceId");
        $this->text = Checks::checkParam($params, "text");
        $this->status = Checks::checkParam($params, "status");
        $this->audio = Checks::checkParam($params, "audio");
        $this->voiceName = Checks::checkParam($params, "voiceName");
        $this->errors = Checks::checkParam($params, "errors");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create an AiSpeech object

    Send an AiSpeech object for creation at the Stark Infra API. The call waits for the synthesis and returns the audio.

    ## Parameters (required):
        - speech [AiSpeech object]: AiSpeech object to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - AiSpeech object with updated attributes
     */
    public static function create($speech, $user = null)
    {
        $payload = [
            "voiceId" => $speech->voiceId,
            "text" => $speech->text
        ];
        $resource = AiSpeech::resource();
        $json = Rest::postRaw($user, API::endpoint($resource["name"]), $payload)->json();
        return API::fromApiJson($resource["maker"], $json[API::lastName($resource["name"])]);
    }

    /**
    # Retrieve a specific AiSpeech

    Receive a single AiSpeech object previously created in the Stark Infra API by its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - expand [array of strings, default null]: extra attributes to compute. Options: "voiceName".
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - AiSpeech object with updated attributes
     */
    public static function get($id, $options = [], $user = null)
    {
        return Rest::getId($user, AiSpeech::resource(), $id, $options);
    }

    /**
    # Retrieve AiSpeeches

    Receive an enumerator of AiSpeech objects previously created in the Stark Infra API. The audio is left out; use get to receive it.
    The API answers this list under "speeches", which the core cannot derive from the resource name, so the pages are read here.

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - expand [array of strings, default null]: extra attributes to compute. Options: "voiceName".
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of AiSpeech objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $remaining = Checks::checkParam($options, "limit");
        do {
            $options["limit"] = is_null($remaining) ? null : min($remaining, 100);
            list($speeches, $cursor) = AiSpeech::page($options, $user);
            foreach ($speeches as $speech) {
                yield $speech;
            }
            $options["cursor"] = $cursor;
            if (!is_null($remaining)) {
                $remaining -= count($speeches);
            }
        } while (!is_null($cursor) && $cursor !== "" && (is_null($remaining) || $remaining > 0));
    }

    /**
    # Retrieve paged AiSpeeches

    Receive a list of up to 100 AiSpeech objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - expand [array of strings, default null]: extra attributes to compute. Options: "voiceName".
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of AiSpeech objects with updated attributes
        - cursor to retrieve the next page of AiSpeech objects, null on the last page
     */
    public static function page($options = [], $user = null)
    {
        $resource = AiSpeech::resource();
        $json = Rest::getRaw($user, API::endpoint($resource["name"]), $options)->json();
        $speeches = array_map(function ($entity) use ($resource) {
            return API::fromApiJson($resource["maker"], $entity);
        }, $json["speeches"]);
        return [$speeches, Checks::checkParam($json, "cursor")];
    }

    private static function resource()
    {
        $speech = function ($array) {
            return new AiSpeech($array);
        };
        return [
            "name" => "AiSpeech",
            "maker" => $speech,
        ];
    }
}
