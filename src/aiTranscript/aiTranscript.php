<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class AiTranscript extends Resource
{

    public $audio;
    public $text;
    public $status;
    public $errors;
    public $created;
    public $updated;

    /**
    # AiTranscript object

    An AiTranscript is the text spoken in a recording you upload. The transcription is synchronous: the object
    returned by 'create' already comes back in "success" status carrying the text.
    When you initialize an AiTranscript, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the object
    to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - audio [string]: base64-encoded recording to be transcribed.

    ## Attributes (return-only):
        - id [string]: unique id returned when the AiTranscript is created. ex: "5656565656565656"
        - text [string]: transcribed text.
        - status [string]: current status of the transcript. Options: "processing", "success", "failed"
        - errors [array of strings]: reasons the transcription failed. Empty while the transcript is healthy.
        - created [DateTime]: creation datetime for the AiTranscript.
        - updated [DateTime]: latest update datetime for the AiTranscript.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->audio = Checks::checkParam($params, "audio");
        $this->text = Checks::checkParam($params, "text");
        $this->status = Checks::checkParam($params, "status");
        $this->errors = Checks::checkParam($params, "errors");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create an AiTranscript object

    Send an AiTranscript object for creation at the Stark Infra API. The call waits for the transcription and returns the text.

    ## Parameters (required):
        - transcript [AiTranscript object]: AiTranscript object to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - AiTranscript object with updated attributes
     */
    public static function create($transcript, $user = null)
    {
        return Rest::postSingle($user, AiTranscript::resource(), $transcript);
    }

    /**
    # Retrieve AiTranscripts

    Receive an enumerator of AiTranscript objects previously created in the Stark Infra API

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of AiTranscript objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        return Rest::getList($user, AiTranscript::resource(), $options);
    }

    /**
    # Retrieve paged AiTranscripts

    Receive a list of up to 100 AiTranscript objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of AiTranscript objects with updated attributes
        - cursor to retrieve the next page of AiTranscript objects, null on the last page
     */
    public static function page($options = [], $user = null)
    {
        return Rest::getPage($user, AiTranscript::resource(), $options);
    }

    private static function resource()
    {
        $transcript = function ($array) {
            return new AiTranscript($array);
        };
        return [
            "name" => "AiTranscript",
            "maker" => $transcript,
        ];
    }
}
