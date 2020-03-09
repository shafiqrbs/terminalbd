<?php

namespace Setting\Bundle\ToolBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class SmsGateWay
{

    private $username;
    private $password;

    /**
     * @var Client
     */
    private $client;

    public function  __construct($username, $password, Client $client)
    {

        $this->username = $username;
        $this->password = $password;
        $this->password = $password;
        $this->client = $client;
    }

    function sendPrevious($msg, $phone){

        try {

            $body = '{"authentication": {"username": "' . $this->username .'","password": "'.$this->password.'"},"messages": [{"sender": "8804445651233","text": "'.$msg.'","recipients": [{"gsm": "'.$phone.'"}]}]}';

            $response = $this->client->post(
                "/api/v3/sendsms/json",
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept'       => '/',
                    ],
                    'body'    => $body,
                ]
            );
            $content  = $response->getBody()->getContents();
            return 'success';

        } catch (RequestException $e) {
            //var_dump($e->getRequest());
            if ($e->hasResponse()) {
                // var_dump($e->getResponse()->getReasonPhrase());
            }
            return 'failed';
        }

    }


    function send($msg, $phone, $sender = ""){

        if(empty($sender)){
            $from = "03590602016";
        }else{
            $from = $sender;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.icombd.com/api/v1/campaigns/sms/1/text/single",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{\"from\":\"{$from}\",\"text\":\"{$msg}\",\"to\":\"{$phone}\"}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Basic dW1hcml0IDcxZmI4ODY4LWI5MGUtNDNkMy05ODFiLTNlY2U1YTI3MDJlNSB0ZXJtaW5hbGJkQGdtYWlsLmNvbQ=="
            ),
        ));
        $response = curl_exec($curl);
        print_r(curl_error($curl));
        curl_close($curl);
      //  echo $response;
        return 'success';

    }
}