<?php

namespace Test\AiBoundarySuite;
use Exception;


exec(escapeshellarg(PHP_BINARY) . " -d error_reporting=" . (E_ALL & ~E_DEPRECATED) . " " . escapeshellarg(__DIR__ . "/aiBoundary.php") . " 2>&1", $output, $status);

if ($status != 0) {
    throw new Exception("failed: " . join("\n", $output));
}
echo "\n" . join("\n", $output);
