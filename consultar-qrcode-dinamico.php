<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/main.php';

$response = $obApiPix->consultCob('WDEV12345678909876543211234');

if (!isset($response['location'])) {
	echo 'Problemas ao consultar PIX dinamico:' . $response;
}

echo $response;
