<?php

namespace StarkInfra;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;


class PixDirector extends Resource
{
    /**
    # PixDirector object

    Pix Directors are used for registering Pix participants` emergency contact information at the Brazilian Central Bank.
    This process is mandatory for all direct Pix participants.
    
    ## Parameters (required):
        - email [string]: email of the PixDirector. ex: "ned.stark@starkbank.com"
        - name [string]: name of the PixDirector. ex: "Edward Stark".
        - password [string]: password of the PixDirector. ex: "12345678"
        - phone [string]: phone of the PixDirector. ex: "+551198989898"
        - taxId [string]: tax ID (CPF/CNPJ) of the PixDirector. ex: "03.300.300/0001-00"
        - teamEmail [string]: team email. ex: "pix.team@company.com"
        - teamPhones [array of strings]: list of phones of the team. ex: ["+5511988889999", "+5511988889998"]
    
    ## Attributes (return-only):
        - id [string]: unique id returned when the PixDirector is created. ex: "5656565656565656"
        - status [string]: current PixDirector status. ex: "success"
    */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> email = Checks::checkParam($params, "email");
        $this-> name = Checks::checkParam($params, "name");
        $this-> password = Checks::checkParam($params, "password");
        $this-> phone = Checks::checkParam($params, "phone");
        $this-> taxId = Checks::checkParam($params, "taxId");
        $this-> teamEmail = Checks::checkParam($params, "teamEmail");
        $this-> teamPhones = Checks::checkParam($params, "teamPhones");
        $this-> status = Checks::checkParam($params, "status");
        
        Checks::checkParams($params);
    }

    /**
    # Create a PixDirector Object
    
    Send a PixDirector object for creation in the Stark Infra API
    
    ## Parameters (required):
        - director [PixDirector Object]: list of PixDirector objects to be created in the API
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixDirector object with updated attributes
    */
    public static function create($director, $user = null)
    {
        return Rest::postSingle($user, PixDirector::resource(), $director);
    }

    private static function resource()
    {
        $director = function ($array) {
            return new PixDirector($array);
        };
        return [
            "name" => "PixDirector",
            "maker" => $director,
        ];
    }
}
