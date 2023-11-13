<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkInfra\PixUser\Statistics;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class PixUser extends Resource
{

    public $statistics;

    /**
    # PixUser object

    Pix Users are used to get fraud statistics of a user. 
    
    ## Parameters (required):
        - id [string]: payer tax ID (CPF or CNPJ) with or without formatting. ex: "01234567890" or "20.018.183/0001-80" 
    
    ## Attributes (return-only):
        - statistics [list of PixUser.Statistics objects]: list of PixUser.Statistics objects. ex: [PixUser.Statistics(after="2023-11-06T18:57:08.325090+00:00", source="pix-key")]
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> statistics = Statistics::parseStatistics(Checks::checkParam($params, "statistics"));

        Checks::checkParams($params);
    }

    /** 
    # Retrieve a PixUser object

    Retrieve the PixUser object linked to your Workspace in the Stark Infra API using its id.

    ## Parameters (required):
        - id [string]: payer tax ID (CPF or CNPJ) with or without formatting. ex: "01234567890" or "20.018.183/0001-80"
    
    ## Parameters (optional):
        - keyId [string]: receiver's PixKey id. ex: "+5511989898989"
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixUser object that corresponds to the given taxId.
    */
    public static function get($id, $keyId=null, $user=null)
    {
        return Rest::getId($user, PixUser::resource(), $id, $keyId);
    }

    private static function resource()
    {
        $user = function ($array) {
            return new PixUser($array);
        };
        return [
            "name" => "PixUser",
            "maker" => $user,
        ];
    }
}
