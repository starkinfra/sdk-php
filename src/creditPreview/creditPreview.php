<?php

namespace StarkInfra;
use StarkCore\Utils\API;
use StarkInfra\Utils\Rest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class CreditPreview extends SubResource
{

    public $credit;
    public $type;

    /**
    # CreditPreview object

    A CreditPreview is used to get information from a credit before taking it.
    This resource can be used to preview credit notes

    ## Parameters (required):
        - credit [CreditNotePreview Object]: Information preview of the informed credit.
        - type [string]: Credit type. Options: "credit-note"
     */
    function __construct(array $params)
    {
        $this-> credit = Checks::checkParam($params, "credit");
        $this-> type = Checks::checkParam($params, "type");

        Checks::checkParams($params);
        
        list($this->credit, $this->type) = self::parseCredit($this->credit, $this->type);
    }

    /**
    # Create CreditPreviews

    Send a list of CreditPreview objects for processing in the Stark Infra API

    ## Parameters (required):
        - previews [array of CreditPreview objects]: array of CreditPreview objects to be created in the API.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - array of CreditPreview objects with updated attributes
     */
    public static function create($previews, $user = null)
    {
        return Rest::post($user, CreditPreview::resource(), $previews);
    }

    private static function resource()
    {
        $preview = function ($array) {
            return new CreditPreview ($array);
        };
        return [
            "name" => "CreditPreview",
            "maker" => $preview,
        ];
    }

    private static function parseCredit($credit, $type)
    {
        if($credit instanceof CreditPreview\CreditNotePreview)
            return [$credit, "credit-note"];

        if(!is_array($credit))
            throw new \Exception("credit must either be 
                a StarkInfra\CreditPreview\CreditNotePreview
                or an array.");

        $makerOptions = [
            "credit-note" => function ($array) {
                return new CreditPreview\CreditNotePreview($array);
            },
        ];

        if (isset($makerOptions[$type]))
            $credit = API::fromApiJson($makerOptions[$type], $credit);

        return [$credit, $type];
    }
}
