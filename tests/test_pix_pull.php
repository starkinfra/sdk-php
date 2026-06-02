<?php

namespace Test;
require_once('vendor/autoload.php');
include_once("tests/utils/rule.php");
include_once("tests/utils/dynamicBrcode.php");


$projectId = $_SERVER["SANDBOX_INFRA_ID"];
$privateKey = $_SERVER["SANDBOX_INFRA_PRIVATE_KEY"];
if (is_null($projectId) || is_null($privateKey)) {
    throw new \Exception("missing test credentials");
}

$project = new \StarkInfra\Project([
    "environment" => "sandbox",
    "id" => $projectId,
    "privateKey" => $privateKey
]);
\StarkInfra\Settings::setUser($project);

echo "\n\nStarting pix pull tests\n";

include_once("tests/pixPullSubscription.php");
include_once("tests/pixPullSubscriptionLog.php");
include_once("tests/pixPullRequest.php");
include_once("tests/pixPullRequestLog.php");

echo "\n\nAll pix pull tests concluded\n\n";
