<?php

namespace StarkInfra\CreditNote;
use StarkCore\Utils\SubResource;


#[\AllowDynamicProperties]
class TemplateReplacements extends SubResource
{

    /**
    # CreditNote\TemplateReplacements object

    The CreditNote\TemplateReplacements object carries the custom values that fill the placeholders
    of the contract template of a CreditNote. Its attributes are free: they depend on the template,
    and every key is kept exactly as written, including the ones the API returns.

    ## Parameters (optional):
        - params [associative array]: placeholder name and its value. Keys are not converted, so use the camelCase
            name of each placeholder. Keys with a null value are not sent.
            ex: new TemplateReplacements(["guarantorName" => "Jon Snow", "guarantorTaxId" => "012.345.678-90"])
    */
    function __construct(array $params = [])
    {
        foreach ($params as $key => $value) {
            $this->$key = $value;
        }
    }
}
