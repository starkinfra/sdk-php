<?php

namespace StarkInfra;
use StarkInfra\Utils\API;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;


class CreditPreview extends Resource
{
    /**
    # CreditPreview object

    A CreditPreview is used to get information from a credit before taking it.
    This resource can be used to preview credit notes

    ## Parameters (required):
        - type [string]: table type that defines the amortization system. Options: "sac", "price", "american", "bullet", "custom"
        - credit [CreditNote.CreditPreview]: Information preview of the informed Credit.
     */
    function __construct(array $params)
    {
        parent::__construct($params);
        
        $this-> type = Checks::checkParam($params, "type");
        $this-> credit = Checks::checkParam($params, "credit");

        Checks::checkParams($params);
        
        list($this->credit, $this->type) = self::parseCredit($this->credit, $this->type);

    }

    /**
    # Create CreditPreviews

    Create CreditPreviews in the Stark Infra API

    ## Parameters (required):
        - previews [array of CreditPreview objects]: CreditPreview objects to be created in the API.

    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was used before function call

    ## Return:
        - Array of CreditPreview objects with updated attributes
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
                a credit note
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
