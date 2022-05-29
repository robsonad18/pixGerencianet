<?php

use App\Pix\Payload;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/main.php";

$requests = [
	"calendario" => [
		"expiracao"	=> 3600
	],
	"devedor" => [
		"cpf"  => "123456789",
		"nome" => "Nome teste"
	],
	"valor" => [
		"original" => "10.00",
	],
	"chave" 		     => "123456789",
	"solicitacaoPagador" => "Pagamento do pedido ".base64_encode(time())
];

$response = $obApiPix->createCob("", $requests);

if (!isset($response["location"])) echo "Problemas ao gerar PIX dinamico:" . $response;


$obPayload = (new Payload)
	->setMerchantName("Robson Lucas")
	->setMerchantCity("SAO PAULO")
	->setAmount($response["valor"]["original"])
	->setTxId($response["txid"])
	->setUniquePayment(true);

$payloadQrCode = $obPayload->getPayload();
$obQrCode = new QrCode($payloadQrCode);
$image = (new Output\Png)->output($obQrCode, 400);

header("Content-Type: image/png");
echo $image;
