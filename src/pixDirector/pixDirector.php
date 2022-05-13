<?php

namespace StarkInfra;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Rest;

class PixDirector extends Resource
{
    /**
    # PixDirector object

    Mandatory data that must be registered within the Central Bank for emergency contact purposes.
    When you initialize a PixDirector, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the list of created objects.
    
    ## Parameters (required):
        - name [string]: name of the PixDirector. ex: "Edward Stark".
        - taxId [string]: tax ID (CPF/CNPJ) of the PixDirector. ex: "03.300.300/0001-00"
        - phone [string]: phone of the PixDirector. ex: "+55-1198989898"
        - email [string]: email of the PixDirector. ex: "ned.stark@starkbank.com"
        - password [string]: password of the PixDirector. ex: "12345678"
        - teamEmail [string]: team email. ex: "pix.team@company.com"
        - teamPhones [list of strings]: list of phones of the team. ex: ["+55-11988889999", "+55-11988889998"]
    
    ## Attributes (return-only):
        - id [string, default null]: unique id returned when the PixDirector is created. ex: "5656565656565656"
        - status [string, default null]: current PixDirector status. ex: "success"
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
        - director [list of PixDirector Object]: list of PixDirector objects to be created in the API
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - PixDirector objects with updated attributes
    */
    public static function create($directors, $user = null)
    {
        return Rest::postSingle($user, PixDirector::resource(), $directors);
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
