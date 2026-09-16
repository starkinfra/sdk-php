<?php

namespace Test\CreditSigner;
use \Exception;
use StarkInfra\CreditNote;
use StarkInfra\CreditSigner;
use Test\CreditNote\TestCreditNote;


class TestCreditSigner
{
    public function resendToken()
    {
        $creditNote = CreditNote::create([TestCreditNote::exampleCCB()])[0];
        if (is_null($creditNote->id)) {
            throw new Exception("failed");
        }

        $signer = $creditNote->signers[0];
        if (is_null($signer->id)) {
            throw new Exception("failed");
        }

        $resentSigner = CreditSigner::resendToken($signer->id);
        if ($resentSigner->id != $signer->id) {
            throw new Exception("failed");
        }
    }
}

echo "\n\nCreditSigner:";

$test = new TestCreditSigner();

echo "\n\t- resend token";
$test->resendToken();
echo " - OK";
