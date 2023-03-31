<?php

namespace StarkInfra;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\Resource;
use StarkCore\Utils\StarkDate;


class CreditHolmes extends Resource
{

    public $taxId;
    public $competence;
    public $tags;
    public $result;
    public $status;
    public $created;
    public $updated;

    /**
    # CreditHolmes object

    CreditHolmes are used to obtain debt information on your customers.
    Before you create a CreditHolmes, make sure you have your customer's express
    authorization to verify their information in the Central Bank's SCR.

    When you initialize a CreditHolmes, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the array of created objects 

    ## Parameters (required):
        - taxId [string]: Customer's tax ID (CPF or CNPJ) for whom the credit operations will be verified. ex: "20.018.183/0001-80"

    ## Parameters (optional):
        - competence [string, default 'two months before current date']: competence month of the operation verification, format: "YYYY-MM". ex: "2021-04"
        - tags [array of strings, default []]: Array of strings for reference when searching for CreditHolmes. ex: ["employees", "monthly"]

    ## Attributes (return-only):
        - id [string]: Unique id returned when the CreditHolmes is created. ex: "5656565656565656"
        - result [string]: Result of the investigation after the case is solved.
        - status [string]: Current status of the CreditHolmes. ex: "created", "failed", "success"
        - created [DateTime]: Creation datetime for the CreditHolmes.
        - updated [DateTime]: Latest update datetime for the CreditHolmes.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> taxId = Checks::checkParam($params, "taxId");
        $this-> competence = Checks::checkParam($params, "competence");
        $this-> tags = Checks::checkParam($params, "tags");
        $this-> result = Checks::checkParam($params, "result");
        $this-> status = Checks::checkParam($params, "status");
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));

        Checks::checkParams($params);
    }

    /**
    # Create CreditHolmes

    Send an array of Credit Holmes objects for creation in the Stark Infra API

    ## Parameters (required):
        - holmes [array of CreditHolmes objects]: array of Credit Holmes objects to be created in the API

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of Credit Holmes objects with updated attributes
     */
    public static function create($holmes, $user = null)
    {
        return Rest::post($user, CreditHolmes::resource(), $holmes);
    }

    /**
    # Retrieve a specific CreditHolmes

    Receive a single CreditHolmes object previously created in the Stark Infra API by passing its id

    ## Parameters (required):
        - id [string]: object unique id. ex: "5656565656565656"

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - CreditHolmes object with updated attributes
     */
    public static function get($id, $user = null)
    {
        return Rest::getId($user, CreditHolmes::resource(), $id);
    }

    /**
    # Retrieve CreditHolmes

    Receive an enumerator of CreditHolmes objects previously created in the Stark Infra API.

    ## Parameters (optional):
        - limit [integer, default null]: maximum number of objects to be retrieved. Unlimited if null. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "created", "failed", "success"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - enumerator of Credit Holmes objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));
        
        return Rest::getList($user, CreditHolmes::resource(), $options);
    }

    /**
    # Retrieve paged Credit Holmes

    Receive a list of up to 100 Credit Holmes objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.

    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call
        - limit [integer, default 100]: maximum number of objects to be retrieved. It must be an integer between 1 and 100. ex: 50
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - status [string, default null]: filter for status of retrieved objects. ex: "created", "failed", "success"
        - tags [array of strings, default null]: tags to filter retrieved objects. ex: ["tony", "stark"]
        - ids [array of strings, default null]: array of ids to filter retrieved objects. ex: ["5656565656565656", "4545454545454545"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call
    
    ## Return:
        - list of Credit Holmes objects with updated attributes
        - cursor to retrieve the next page of CreditHolmes objects
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, CreditHolmes::resource(), $options);
    }

    private static function resource()
    {
        $holmes = function ($array) {
            return new CreditHolmes($array);
        };
        return [
            "name" => "CreditHolmes",
            "maker" => $holmes,
        ];
    }
}
