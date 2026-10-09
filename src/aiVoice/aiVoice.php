<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\API;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class AiVoice extends Resource
{

    public $audio;
    public $name;
    public $description;
    public $language;
    public $gender;
    public $status;
    public $errors;
    public $created;
    public $updated;

    /**
    # AiVoice object

    An AiVoice is a voice cloned from a recording you upload. Once cloned, it can read any text out loud through
    an AiSpeech, and it can be attached to an AiAgent so every reply carries a speech ready to be synthesized.
    Cloning is asynchronous: the voice is created in "processing" status and moves to "success" when it is ready
    to speak, or to "failed" when the recording could not be cloned.
    When you initialize an AiVoice, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the object
    to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - audio [string]: base64-encoded recording of the speaker. MP3, WAV, OGG, FLAC and WebM are accepted. Up to 10000000 characters.

    ## Parameters (optional):
        - name [string, default null]: name of the voice. Up to 100 characters. Defaults to the voice's own id. ex: "Helena"
        - description [string, default null]: free-text description of the voice. Up to 1000 characters.
        - language [string, default null]: language the voice speaks. Options: "portuguese", "english". The API defaults to "portuguese".
        - gender [string, default null]: gender of the voice. Options: "male", "female", "neutral"

    ## Attributes (return-only):
        - id [string]: unique id returned when the AiVoice is created. This is the voiceId you send to other AI resources. ex: "5656565656565656"
        - status [string]: current status of the voice. Options: "processing", "success", "failed". Only a voice in "success" can speak.
        - errors [array of strings]: reasons the cloning failed. Empty while the voice is healthy.
        - created [DateTime]: creation datetime for the AiVoice.
        - updated [DateTime]: latest update datetime for the AiVoice.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->audio = Checks::checkParam($params, "audio");
        $this->name = Checks::checkParam($params, "name");
        $this->description = Checks::checkParam($params, "description");
        $this->language = Checks::checkParam($params, "language");
        $this->gender = Checks::checkParam($params, "gender");
        $this->status = Checks::checkParam($params, "status");
        $this->errors = Checks::checkParam($params, "errors");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create an AiVoice object

    Send an AiVoice object for creation at the Stark Infra API and start cloning it.
    The call returns immediately with the voice in "processing" status.

    ## Parameters (required):
        - voice [AiVoice object]: AiVoice object to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - AiVoice object with updated attributes
     */
    public static function create($voice, $user = null)
    {
        $payload = [
            "audio" => $voice->audio,
            "name" => $voice->name,
            "description" => $voice->description,
            "language" => $voice->language,
            "gender" => $voice->gender
        ];
        $resource = AiVoice::resource();
        $json = Rest::postRaw($user, API::endpoint($resource["name"]), $payload)->json();
        return API::fromApiJson($resource["maker"], $json[API::lastName($resource["name"])]);
    }

    /**
    # Retrieve AiVoices

    Receive an enumerator of AiVoice objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of AiVoice objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        return Rest::getList($user, AiVoice::resource(), $options);
    }

    /**
    # Retrieve paged AiVoices

    Receive a list of up to 100 AiVoice objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of AiVoice objects with updated attributes
        - cursor to retrieve the next page of AiVoice objects, null on the last page
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, AiVoice::resource(), $options);
    }

    /**
    # Delete AiVoices

    Delete up to 100 AiVoices at once.

    ## Parameters (required):
        - ids [array of strings]: ids of the AiVoices to be deleted. Up to 100 ids. ex: ["5656565656565656", "4545454545454545"]

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of deleted AiVoice objects
     */
    public static function delete($ids, $user = null)
    {
        $resource = AiVoice::resource();
        $json = Rest::deleteRaw($user, API::endpoint($resource["name"]), null, null, true, ["ids" => $ids])->json();
        return array_map(function ($entity) use ($resource) {
            return API::fromApiJson($resource["maker"], $entity);
        }, $json[API::lastNamePlural($resource["name"])]);
    }

    private static function resource()
    {
        $voice = function ($array) {
            return new AiVoice($array);
        };
        return [
            "name" => "AiVoice",
            "maker" => $voice,
        ];
    }
}
