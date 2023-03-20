<?php

namespace services\oauth;

class VKontakteOAuth2Service
{
    protected $client_id; // ID приложения
    protected $client_secret; // Защищённый ключ
    protected $redirect_uri; // Адрес сайта
    protected $url = 'http://oauth.vk.com/authorize';
    protected $params;

    public function __construct($redirectUrl)
    {
        $this->setParams($redirectUrl);
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->url . '?' . urldecode(http_build_query($this->getParams()));
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function getTokenParams($code): array
    {
        return [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
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
     * @return void
     */
    protected function setParams($redirectUrl): void
    {
        $this->client_id = getenv('VK_ID');
        $this->client_secret = getenv('VK_SECRET_KEY');
        $this->redirect_uri = getenv('VK_REDIRECT_URI') . $redirectUrl;

        $this->params = [
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'response_type' => 'code'
        ];
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function setParam($name, $value): void
    {
        $this->params[$name] = $value;
    }

}