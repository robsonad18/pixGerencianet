<?php

require __DIR__ . "/vendor/autoload.php";

use App\Pix\Payload;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

$obPayload = (new Payload)
    ->setPixKey("1899999999")
    ->setDescription("Pagamento do pedido 666")
    ->setMerchantName("Robson Lucas")
    ->setMerchantCity("SAO PAULO")
    ->setAmount("0")
    ->setTxId(sha1(true));

$payloadQrCode = $obPayload->getPayload();
$obQrCode = new QrCode($payloadQrCode);
$image = (new Output\Png)->output($obQrCode, 400);

header("Content-Type: image/png");
echo $image;
