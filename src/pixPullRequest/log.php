<?php

namespace StarkInfra\PixPullRequest;
use StarkCore\Utils\API;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;
use StarkInfra\PixPullRequest;


class Log extends Resource
{

    public $request;
    public $type;
    public $errors;
    public $created;

    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> request = Checks::checkParam($params, "request");
        $this-> type = Checks::checkParam($params, "type");
        $this-> errors = Checks::checkParam($params, "errors");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));

        Checks::checkParams($params);
    }

    public static function get($id, $user = null)
    {
        return Rest::getId($user, Log::resource(), $id);
    }

    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getList($user, Log::resource(), $options);
    }

    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        return Rest::getPage($user, Log::resource(), $options);
    }

    private static function resource()
    {
        $requestLog = function ($array) {
            $request = function ($array) {
                return new PixPullRequest($array);
            };
            $array["request"] = API::fromApiJson($request, $array["request"]);
            return new Log($array);
        };
        return [
            "name" => "PixPullRequestLog",
            "maker" => $requestLog,
        ];
    }
}
