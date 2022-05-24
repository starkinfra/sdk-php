<?php

namespace StarkInfra;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\Checks;
use EllipticCurve\PrivateKey;


class User extends Resource
{
    function __construct(&$params)
    {
        parent::__construct($params);

        $this->pem = Checks::checkPrivateKey(Checks::checkParam($params, "privateKey"));
        $this->environment = Checks::checkEnvironment(Checks::checkParam($params, "environment"));
    }

    public function privateKey()
    {
        return PrivateKey::fromPem($this->pem);
    }
}
