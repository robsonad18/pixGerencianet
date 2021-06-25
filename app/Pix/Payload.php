<?php

namespace App\Pix;

class Payload
{
    /**
     * IDs do Payload do Pix
     * @var string
     */
    const ID_PAYLOAD_FORMAT_INDICATOR                   = '00';
    const ID_POINT_OF_INITIATION_METHOD                 = '01';
    const ID_MERCHANT_ACCOUNT_INFORMATION               = '26';
    const ID_MERCHANT_ACCOUNT_INFORMATION_GUI           = '00';
    const ID_MERCHANT_ACCOUNT_INFORMATION_KEY           = '01';
    const ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION   = '02';
    const ID_MERCHANT_ACCOUNT_INFORMATION_URL           = '25';
    const ID_MERCHANT_CATEGORY_CODE                     = '52';
    const ID_TRANSACTION_CURRENCY                       = '53';
    const ID_TRANSACTION_AMOUNT                         = '54';
    const ID_COUNTRY_CODE                               = '58';
    const ID_MERCHANT_NAME                              = '59';
    const ID_MERCHANT_CITY                              = '60';
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE             = '62';
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID        = '05';
    const ID_CRC16                                      = '63';

    /**
     * Chave pix
     * 
     * @var string
     */
    private $pixKey;

    /**
     * Descrição do pagamento
     * 
     * @var string
     */
    private $description;

    /**
     * Nome do titular da conta
     * 
     * @var string
     */
    private $merchantName;

    /**
     * Cidade do titular da conta
     * 
     * @var string
     */
    private $merchantCity;

    /**
     * Id da transação pix
     * 
     * @var string
     */
    private $txid;

    /**
     * Valor da transação
     * 
     * @var string
     */
    private $amount;


    /**
     * Define se o pagamento deve ser feito apenas uma vez
     * @var false
     */
    private $uniquePayment = false;


    /**
     * Url do payload dinamico
     * @var mixed
     */
    private $url;



    /**
     * Seta se o pagamento sera feito um vez ou varias
     * @param mixed $uniquePayment 
     * @return $this 
     */
    public function setUniquePayment($uniquePayment)
    {
        $this->uniquePayment = $uniquePayment;
        return $this;
    }



    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }




    /**
     * Metodo responsavel por setar a PIX Key
     * 
     * @param string $pixKey 
     * @return $this 
     */
    public function setPixKey(string $pixKey)
    {
        $this->pixKey = $pixKey;
        return $this;
    }


    /**
     * Responsavel por setar a descrição do pagamento
     * 
     * @param string $description 
     * @return $this 
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }


    /**
     * Responsavel por setar o nome do titular da conta
     * 
     * @param string $merchantName 
     * @return $this 
     */
    public function setMerchantName(string $merchantName)
    {
        $this->merchantName = $merchantName;
        return $this;
    }


    /**
     * Responsavel por setar a cidade do titular da conta
     * 
     * @param string $city 
     * @return $this 
     */
    public function setMerchantCity(string $city)
    {
        $this->merchantCity = $city;
        return $this;
    }



    /**
     * Responsa por setar o Id da transação pix
     * 
     * @param string $txid 
     * @return $this 
     */
    public function setTxId(string $txid)
    {
        $this->txid = $txid;
        return $this;
    }


    /**
     * Responsavel por setar o valor da transação
     * 
     * @param string $amount 
     * @return $this 
     */
    public function setAmount(string $amount)
    {
        $this->amount = number_format($amount, 2, '.', '');
        return $this;
    }



    /**
     * Responsavel por retornar o valor completo de um objeto do payload
     * 
     * @param string $id 
     * @param string $value 
     * @return void 
     */
    private function getValue(string $id, string $value)
    {
        $size = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
        return $id . $size . $value;
    }



    /**
     * Responsavel por retornar os valores completos da informação da conta
     * 
     * @return void 
     */
    private function getMerchantAccountInformation()
    {
        // Dominio do banco central
        $gui = $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_GUI, 'br.gov.bcb.pix');
        // Chave PIX
        $key = strlen($this->pixKey) ? $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_KEY, $this->pixKey) : '';
        // Descrição do pagamento
        $description = strlen($this->description) ? $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION, $this->description) : '';
        // URL DO QRCODE DINAMICO
        $url = strlen($this->url) ? $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_URL, preg_replace('/^https?\:\/\//', '', $this->url)) : '';
        // Valor completo da conta
        return $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION, $gui . $key . $description . $url);
    }



    /**
     * Responsavel por retornar os valores completos do campo adicional do pix (TXID)
     * @return string
     * 
     * @return string 
     */
    private function getAdditionalDataFiledTemplate()
    {
        // TXID
        $txid = $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID, $this->txid);
        return $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE, $txid ?? '');
    }



    /**
     * metodo responsavel por retornar o valor do ID_POINT_OF_INITIATION_METHOD
     * @return void 
     */
    private function getUniquePayment()
    {
        return $this->uniquePayment ? $this->getValue(self::ID_POINT_OF_INITIATION_METHOD, '12') : '';
    }



    /**
     * Responsavel por gerar o codigo do Payload
     * 
     * @return void 
     */
    public function getPayload()
    {
        // Cria o payload
        $payload = $this->getValue(self::ID_PAYLOAD_FORMAT_INDICATOR, '01') .
            $this->getUniquePayment() .
            $this->getMerchantAccountInformation() .
            $this->getValue(self::ID_MERCHANT_CATEGORY_CODE, '0000') .
            $this->getValue(self::ID_TRANSACTION_CURRENCY, '986') .
            $this->getValue(self::ID_TRANSACTION_AMOUNT, $this->amount) .
            $this->getValue(self::ID_COUNTRY_CODE, 'BR') .
            $this->getValue(self::ID_MERCHANT_NAME, $this->merchantName) .
            $this->getValue(self::ID_MERCHANT_CITY, $this->merchantCity) .
            $this->getAdditionalDataFiledTemplate();
        // Retorna o payload + CRC16
        return $payload . $this->getCRC16($payload);
    }


    /**
     * Método responsável por calcular o valor da hash de validação do código pix
     * @return string
     */
    private function getCRC16($payload)
    {
        //ADICIONA DADOS GERAIS NO PAYLOAD
        $payload .= self::ID_CRC16 . '04';

        //DADOS DEFINIDOS PELO BACEN
        $polinomio = 0x1021;
        $resultado = 0xFFFF;

        //CHECKSUM
        if (($length = strlen($payload)) > 0) {
            for ($offset = 0; $offset < $length; $offset++) {
                $resultado ^= (ord($payload[$offset]) << 8);
                for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                    if (($resultado <<= 1) & 0x10000) $resultado ^= $polinomio;
                    $resultado &= 0xFFFF;
                }
            }
        }

        //RETORNA CÓDIGO CRC16 DE 4 CARACTERES
        return self::ID_CRC16 . '04' . strtoupper(dechex($resultado));
    }
}
