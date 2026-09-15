<?php

namespace StarkInfra\BusinessAccountRequest;
use StarkCore\Utils\Checks;
use StarkCore\Utils\SubResource;


class Owner extends SubResource
{

    public $taxId;
    public $name;
    public $role;
    public $identityId;
    public $validatorLink;
    public $status;

    /**
    # BusinessAccountRequest\Owner object

    The Owner object represents a company owner referenced by a BusinessAccountRequest. Each owner
    completes its own identity verification through an independent webview. It is embedded on the
    parent's owners field and has no endpoints of its own.

    ## Parameters (required):
        - taxId [string]: owner's tax ID (CPF). ex: "012.345.678-90"
        - name [string]: owner's full name (minimum 5 characters). ex: "Jamie Lannister"
        - role [string]: owner's role in the company. Options: "partner", "representative"

    ## Attributes (return-only):
        - identityId [string]: unique id of the identity verification linked to this owner. ex: "5709594221805568"
        - validatorLink [string]: webview link to be delivered to the owner to complete biometrics and document capture. Treat it as a credential.
        - status [string]: current status of the owner verification. Options: "created", "approved", "denied"
     */
    function __construct(array $params)
    {
        $this->taxId = Checks::checkParam($params, "taxId");
        $this->name = Checks::checkParam($params, "name");
        $this->role = Checks::checkParam($params, "role");
        $this->identityId = Checks::checkParam($params, "identityId");
        $this->validatorLink = Checks::checkParam($params, "validatorLink");
        $this->status = Checks::checkParam($params, "status");

        Checks::checkParams($params);
    }
}
