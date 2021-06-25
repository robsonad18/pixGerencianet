<?php

namespace App\Pix;


class Api
{
    /**
     * URL base do PSP
     * 
     * @var string
     */
    private $baseUrl;

    /**
     * Client ID do oAuth2 do PSP
     * 
     * @var string
     */
    private $clientId;

    /**
     * Client secret do oAuth2 do PSP
     * 
     * @var string
     */
    private $clientSecret;

    /**
     * Caminho absoluto até o arquivo do certificado
     * 
     * @var string
     */
    private $certificate;


    public function __construct($baseUrl, $clientId, $clientSecret, $certificate)
    {
        $this->baseUrl       = $baseUrl;
        $this->clientId      = $clientId;
        $this->clientSecret  = $clientSecret;
        $this->certificate   = $certificate;
    }



    /**
     * Metodo responsavel por criar uma cobrança imediata
     * @param string $txId 
     * @param mixed $request 
     * @return mixed 
     */
    public function createCob(string $txId, $request)
    {
        return $this->send('PUT', '/v2/cob/' . $txId, $request);
    }



    /**
     * Metodo responsavel por consultar cob conforme o txid passado
     * @param string $txId 
     * @param mixed $request 
     * @return mixed 
     */
    public function consultCob(string $txId)
    {
        return $this->send('GET', '/v2/cob/' . $txId);
    }



    private function send(string $method, string $resource, $request = [])
    {
        // ENDPOINT COMPLETO
        $endpoint = $this->baseUrl . $resource;
        // HEADER
        $headers = [
            'Cache-Control: no-cache',
            'Content-type: application/json',
            'Authorization: Bearer ' . $this->getAccessToken()
        ];
        // CONFIGURACAO DO CURL
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL             => $endpoint,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_CUSTOMREQUEST   => $method,
            CURLOPT_SSLCERT         => '',
            CURLOPT_HTTPHEADER      => $headers
        ]);

        switch ($method) {
            case 'POST':
            case 'PUT':
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));
                break;
        }


        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }


    /**
     * Metodo responsavel por obter o token de acesso as APis Pix
     * @return void 
     */
    private function getAccessToken()
    {
        // ENDPOINT COMPLETO
        $endpoint = $this->baseUrl . '/oauth/token';
        // HEADER
        $headers = [
            'Content-type: application/json'
        ];
        // CORPO DA REQUISICAO
        $request = [
            'grant_type' => 'client_credentials'
        ];
        // CONFIGURACAO DO CURL
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL             => $endpoint,
            CURLOPT_USERPWD         => $this->clientId . ':' . $this->clientSecret,
            CURLOPT_HTTPAUTH        => CURLAUTH_BASIC,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS      => json_encode($request),
            CURLOPT_SSLCERT         => '',
            CURLOPT_HTTPHEADER      => $headers
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $responseArray = json_decode($response, true);

        return $responseArray['access_token'] ?? '';
    }
}
