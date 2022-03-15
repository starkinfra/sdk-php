<?php

namespace Test;
require_once("vendor/autoload.php");
require_once("src/init.php");
include_once("tests/utils/rule.php");
require_once('vendor/autoload.php');


$projectId = $_SERVER["SANDBOX_ID"]; # "9999999999999999",
$privateKey = $_SERVER["SANDBOX_PRIVATE_KEY"]; # "-----BEGIN EC PRIVATE KEY-----\nMHQCAQEEIBEcEJZLk/DyuXVsEjz0w4vrE7plPXhQxODvcG1Jc0WToAcGBSuBBAAK\noUQDQgAE6t4OGx1XYktOzH/7HV6FBukxq0Xs2As6oeN6re1Ttso2fwrh5BJXDq75\nmSYHeclthCRgU8zl6H1lFQ4BKZ5RCQ==\n-----END EC PRIVATE KEY-----"

if (is_null($projectId) || is_null($privateKey)) {
    throw new \Exception("missing test credentials");
}

$project = new \StarkInfra\Project([
    "environment" => "sandbox",
    "id" => $projectId,
    "privateKey" => $privateKey
]);
\StarkInfra\Settings::setUser($project);

echo "\n\nStarting tests\n";

include_once("issuingBalance.php");
include_once("issuingBin.php");
include_once("issuingCard.php");
include_once("issuingCardLog.php");
include_once("issuingHolder.php");
include_once("issuingHolderLog.php");
include_once("issuingInvoice.php");
include_once("issuingInvoiceLog.php");
include_once("issuingPurchase.php");
include_once("issuingPurchaseLog.php");
include_once("issuingTransaction.php");
include_once("issuingWithdrawal.php");
include_once("issuingAuthorization.php");
include_once("key.php");

echo "\n\nAll tests concluded\n\n";
