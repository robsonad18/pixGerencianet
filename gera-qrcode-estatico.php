<?php

require __DIR__ . '/vendor/autoload.php';

use App\Pix\Payload;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

// Instancia principal do payload pix
$obPayload = (new Payload)
    ->setPixKey('1899999999')
    ->setDescription("Pagamento do pedido 666")
    ->setMerchantName('Robson Lucas')
    ->setMerchantCity('SAO PAULO')
    ->setAmount('0')
    ->setTxId(sha1(true));

// Codigo de pagamento pix
$payloadQrCode = $obPayload->getPayload();
// QR Code
$obQrCode = new QrCode($payloadQrCode);
// Imagem do Qrcode
$image = (new Output\Png)->output($obQrCode, 400);

header('Content-Type: image/png');
echo $image;
