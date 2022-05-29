<?php

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/main.php";

$response = $obApiPix->consultCob("");

if (!isset($response["location"])) echo "Problemas ao consultar PIX dinamico:" . $response;

echo $response;
