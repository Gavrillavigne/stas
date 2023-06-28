<?php

namespace services\oauth;

use common\models\User;

class MailRuOAuth2Service extends OAuth2Service
{
    /**
     * @param $redirectUrl
     * @return void
     */
    protected function setParams($redirectUrl): void
    {
        $this->clientId = getenv('MAILRU_ID');
        $this->clientSecret = getenv('MAILRU_SECRET_KEY');
        $this->redirectUri = getenv('REDIRECT_URI') . $redirectUrl;
        $this->apiUrl = 'https://connect.mail.ru/oauth/authorize';

        $this->params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code'
        ];
    }

}