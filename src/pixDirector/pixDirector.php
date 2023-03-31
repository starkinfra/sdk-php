<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class PixDirector extends SubResource
{

    public $email;
    public $name;
    public $password;
    public $phone;
    public $taxId;
    public $teamEmail;
    public $teamPhones;
    public $status;

    /**
    # PixDirector object

    Mandatory data that must be registered within the Central Bank for emergency contact purposes.
    
    When you initialize a PixDirector, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the list of created objects.
    
    ## Parameters (required):
        - name [string]: name of the PixDirector. ex: "Edward Stark".
        - taxId [string]: tax ID (CPF/CNPJ) of the PixDirector. ex: "03.300.300/0001-00"
        - phone [string]: phone of the PixDirector. ex: "+551198989898"
        - email [string]: email of the PixDirector. ex: "ned.stark@starkbank.com"
        - password [string]: password of the PixDirector. ex: "12345678"
        - teamEmail [string]: team email. ex: "pix.team@company.com"
        - teamPhones [array of strings]: list of phones of the team. ex: ["+5511988889999", "+5511988889998"]
    
    ## Attributes (return-only):
        - status [string]: current PixDirector status. ex: "success"
    */
    function __construct(array $params)
    {
        $this-> name = Checks::checkParam($params, "name");
        $this-> taxId = Checks::checkParam($params, "taxId");
        $this-> phone = Checks::checkParam($params, "phone");
        $this-> email = Checks::checkParam($params, "email");
        $this-> password = Checks::checkParam($params, "password");
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
