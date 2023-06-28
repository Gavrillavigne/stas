<?php

namespace services\oauth;

use GuzzleHttp\Client;

class VKontakteOAuth2Service extends OAuth2Service
{
    public function getCode()
    {
        if (!empty($_GET['code'])) {
            return $_GET['code'];
        }

        header('Location: ' . $this->getLink());

//        $client = new Client(['base_uri' => $this->apiUrl]);
//        $client->request('GET', $this->getLink());
    }

    public function getTokenParams($code): array
    {
        return [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $code
        ];
    }

    public function getUserParams($token): array
    {
        return [
            'uids' => $token['user_id'],
            'fields' => 'uid,first_name,last_name,screen_name,sex,bdate,photo_big',
            'access_token' => $token['access_token'],
            'v' => '5.103'
        ];
    }

    /**
     * @param $redirectUrl
     * @return void
     */
    protected function setParams($redirectUrl): void
    {
        $this->clientId = getenv('VK_ID');
        $this->clientSecret = getenv('VK_SECRET_KEY');
        $this->redirectUri = getenv('REDIRECT_URI') . $redirectUrl . 'vk/';
        $this->apiUrl = 'http://oauth.vk.com/authorize';

        $this->params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'state' => 'vk'
        ];
    }

//    /**
//     * @param $name
//     * @param $value
//     * @return void
//     */
//    public function setParam($name, $value): void
//    {
//        $this->params[$name] = $value;
//    }

}