<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\API;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;


class AiKnowledgeBase extends Resource
{

    public $name;
    public $rootUrl;
    public $isRecursive;
    public $tags;
    public $status;
    public $created;
    public $updated;

    /**
    # AiKnowledgeBase object

    An AiKnowledgeBase turns a website into material an AiAgent can read. You give it a root URL;
    Stark Infra crawls the page, follows its links, converts everything to Markdown and indexes it for retrieval.
    When you initialize an AiKnowledgeBase, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the object
    to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - name [string]: name of the knowledge base. Between 1 and 100 characters. ex: "Product Documentation"
        - rootUrl [string]: absolute http or https URL the crawl starts from. ex: "https://docs.starkinfra.com"

    ## Parameters (optional):
        - isRecursive [bool, default null]: whether the crawl may follow links into other subdomains of the root URL's registered domain. The API defaults to true. ex: false
        - tags [array of strings, default null]: array of up to 100 strings for reference when searching for AiKnowledgeBases. ex: ["support", "public"]

    ## Attributes (return-only):
        - id [string]: unique id returned when the AiKnowledgeBase is created. ex: "5656565656565656"
        - status [string]: current status of the knowledge base. Options: "processing", "success", "failed". An agent retrieves from a base only once it reaches "success".
        - created [DateTime]: creation datetime for the AiKnowledgeBase.
        - updated [DateTime]: latest update datetime for the AiKnowledgeBase.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this->name = Checks::checkParam($params, "name");
        $this->rootUrl = Checks::checkParam($params, "rootUrl");
        $this->isRecursive = Checks::checkParam($params, "isRecursive");
        $this->tags = Checks::checkParam($params, "tags");
        $this->status = Checks::checkParam($params, "status");
        $this->created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this->updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create an AiKnowledgeBase object

    Send an AiKnowledgeBase object for creation at the Stark Infra API and start crawling it.
    The call returns immediately with the knowledge base in "processing" status.

    ## Parameters (required):
        - knowledgeBase [AiKnowledgeBase object]: AiKnowledgeBase object to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - AiKnowledgeBase object with updated attributes
     */
    public static function create($knowledgeBase, $user = null)
    {
        $resource = AiKnowledgeBase::resource();
        $payload = array_filter([
            "name" => $knowledgeBase->name,
            "rootUrl" => $knowledgeBase->rootUrl,
            "isRecursive" => $knowledgeBase->isRecursive,
            "tags" => $knowledgeBase->tags
        ], function ($value) {
            return !is_null($value);
        });
        $json = Rest::postRaw($user, API::endpoint($resource["name"]), $payload)->json();
        return API::fromApiJson($resource["maker"], $json["knowledgeBase"]);
    }

    /**
    # Retrieve a specific AiKnowledgeBase

    Receive a single AiKnowledgeBase object previously created in the Stark Infra API by its id.
    This is the call to poll while the crawl runs.

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - AiKnowledgeBase object with updated attributes
     */
    public static function get($id, $user = null)
    {
        $resource = AiKnowledgeBase::resource();
        $json = Rest::getRaw($user, API::endpoint($resource["name"]) . "/" . Checks::checkId($id))->json();
        return API::fromApiJson($resource["maker"], $json["knowledgeBase"]);
    }

    /**
    # Retrieve AiKnowledgeBases

    Receive an enumerator of AiKnowledgeBase objects previously created in the Stark Infra API.
    The API answers this list under "knowledgeBases", which the core cannot derive from the resource name, so the pages are read here.

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - name [string, default null]: case-insensitive substring of the name to filter retrieved objects. ex: "docs"
        - status [string, default null]: filter for status of retrieved objects. Options: "processing", "success", "failed"
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of AiKnowledgeBase objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $remaining = Checks::checkParam($options, "limit");
        do {
            $options["limit"] = is_null($remaining) ? null : min($remaining, 100);
            list($knowledgeBases, $cursor) = AiKnowledgeBase::page($options, $user);
            foreach ($knowledgeBases as $knowledgeBase) {
                yield $knowledgeBase;
            }
            $options["cursor"] = $cursor;
            if (!is_null($remaining)) {
                $remaining -= count($knowledgeBases);
            }
        } while (!is_null($cursor) && $cursor !== "" && (is_null($remaining) || $remaining > 0));
    }

    /**
    # Retrieve paged AiKnowledgeBases

    Receive a list of up to 100 AiKnowledgeBase objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - name [string, default null]: case-insensitive substring of the name to filter retrieved objects. ex: "docs"
        - status [string, default null]: filter for status of retrieved objects. Options: "processing", "success", "failed"
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - list of AiKnowledgeBase objects with updated attributes
        - cursor to retrieve the next page of AiKnowledgeBase objects, null on the last page
     */
    public static function page($options = [], $user = null)
    {
        $resource = AiKnowledgeBase::resource();
        $json = Rest::getRaw($user, API::endpoint($resource["name"]), $options)->json();
        $knowledgeBases = array_map(function ($entity) use ($resource) {
            return API::fromApiJson($resource["maker"], $entity);
        }, $json["knowledgeBases"]);
        return [$knowledgeBases, Checks::checkParam($json, "cursor")];
    }

    /**
    # Update AiKnowledgeBase entity

    Rename a knowledge base, retag it or change whether its crawl is recursive. The root URL cannot be changed.
    Only the parameters you give are sent.

    ## Parameters (required):
        - id [string]: AiKnowledgeBase unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - name [string, default null]: new name of the knowledge base. Between 1 and 100 characters.
        - isRecursive [bool, default null]: whether the next crawl may follow links into other subdomains of the root URL's registered domain.
        - tags [array of strings, default null]: new array of up to 100 strings. Replaces the current list.
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - target AiKnowledgeBase with updated attributes
     */
    public static function update($id, $options = [], $user = null)
    {
        $payload = array_filter([
            "name" => Checks::checkParam($options, "name"),
            "isRecursive" => Checks::checkParam($options, "isRecursive"),
            "tags" => Checks::checkParam($options, "tags")
        ], function ($value) {
            return !is_null($value);
        });
        $resource = AiKnowledgeBase::resource();
        $json = Rest::patchRaw($user, API::endpoint($resource["name"]) . "/" . Checks::checkId($id), (object) $payload)->json();
        return API::fromApiJson($resource["maker"], $json["knowledgeBase"]);
    }

    /**
    # List the pages of an AiKnowledgeBase

    Receive every page the crawler has seen, grouped by host. While a crawl is running this is the live picture,
    merged with the last finished one.

    ## Parameters (required):
        - id [string]: AiKnowledgeBase unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array mapping each host to its array of pages. Each page has originalUrl, storageUrl and status ("pending", "success" or "failed")
     */
    public static function hosts($id, $user = null)
    {
        $resource = AiKnowledgeBase::resource();
        $json = Rest::getRaw($user, API::endpoint($resource["name"]) . "/" . Checks::checkId($id) . "/hosts")->json();
        return $json["hosts"];
    }

    /**
    # Delete AiKnowledgeBases

    Delete up to 100 AiKnowledgeBases at once. Agents still referencing a deleted base simply retrieve nothing from it.

    ## Parameters (required):
        - ids [array of strings]: ids of the AiKnowledgeBases to be deleted. Up to 100 ids. ex: ["5656565656565656", "4545454545454545"]

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of deleted AiKnowledgeBase objects
     */
    public static function delete($ids, $user = null)
    {
        $resource = AiKnowledgeBase::resource();
        $json = Rest::deleteRaw($user, API::endpoint($resource["name"]), null, null, true, ["ids" => $ids])->json();
        return array_map(function ($entity) use ($resource) {
            return API::fromApiJson($resource["maker"], $entity);
        }, $json["knowledgeBases"]);
    }

    private static function resource()
    {
        $knowledgeBase = function ($array) {
            return new AiKnowledgeBase($array);
        };
        return [
            "name" => "AiKnowledgeBase",
            "maker" => $knowledgeBase,
        ];
    }
}
