<?php

namespace StarkInfra\BusinessAccountRequest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class Address extends SubResource
{

    public $street;
    public $number;
    public $neighborhood;
    public $city;
    public $state;
    public $zipCode;
    public $complement;

    /**
    # BusinessAccountRequest\Address object

    The Address object is the structured address of the company referenced by a
    BusinessAccountRequest. It is embedded on the parent's address field and has no endpoints of its own.

    ## Parameters (required):
        - street [string]: street name. ex: "Av. Faria Lima"
        - number [string]: street number. ex: "2000"
        - neighborhood [string]: neighborhood / district. ex: "Itaim Bibi"
        - city [string]: city. ex: "Sao Paulo"
        - state [string]: state (BR 2-letter code). ex: "SP"
        - zipCode [string]: ZIP code (BR CEP), formatted or digit-only. ex: "04538-132"

    ## Parameters (optional):
        - complement [string, default null]: address complement. ex: "Sala 42"
     */
    function __construct(array $params)
    {
        $this->street = Checks::checkParam($params, "street");
        $this->number = Checks::checkParam($params, "number");
        $this->neighborhood = Checks::checkParam($params, "neighborhood");
        $this->city = Checks::checkParam($params, "city");
        $this->state = Checks::checkParam($params, "state");
        $this->zipCode = Checks::checkParam($params, "zipCode");
        $this->complement = Checks::checkParam($params, "complement");

        Checks::checkParams($params);
    }
}
