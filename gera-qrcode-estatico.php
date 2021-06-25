<?php

require __DIR__ . '/vendor/autoload.php';

use App\Pix\Payload;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

// Instancia principal do payload pix
$obPayload = (new Payload)
    ->setPixKey('18997272790')
    ->setDescription("Pagamento do pedido 666")
    ->setMerchantName('Robson Lucas')
    ->setMerchantCity('SAO PAULO')
    ->setAmount('100.00')
    ->setTxId('wdev1234');

// Codigo de pagamento pix
$payloadQrCode = $obPayload->getPayload();
// QR Code
$obQrCode = new QrCode($payloadQrCode);
// Imagem do Qrcode
$image = (new Output\Png)->output($obQrCode, 400);

// header('Content-Type: image/png');
// echo $image;
?>

<h1>Qr code estatico PIX</h1>

<br />

<img src="data:image/png;base64, <?= base64_encode($image) ?>" alt="">

<br />
<br />

Codigo PIX:<br />
<strong><?= $payloadQrCode ?></strong>
