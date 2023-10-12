<?php

namespace app\services\oauth;

use app\dictionaries\AuthDictionary;
use app\entities\User;
use app\services\IAuthService;
use stdClass;

class VKontakteOAuth2Service extends OAuth2Service implements IAuthService
{
    /**
     * @return mixed|void
     */
    public function getCode()
    {
        if (!empty($_GET['code'])) {
            return $_GET['code'];
        }

        header('Location: ' . $this->getLink());
    }

    /**
     * @param $code
     * @return array
     */
    public function getTokenParams($code): array
    {
        return [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $code
        ];
    }

    /**
     * @param $token
     * @return array
     */
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

    /**
     * @return array
     */
    private function getUserSocial(): array
    {
        $code = $this->getCode();

        if (!empty($code)) {
            $oauthParams = $this->getTokenParams($code);
            $token = json_decode(file_get_contents('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($oauthParams))), true);

            if (!empty($token['access_token'])) {
                $params = $this->getUserParams($token);
                $userInfo = json_decode(file_get_contents('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query($params))), true);

                if (isset($userInfo['response'][0]['id'])) {
                    return $userInfo['response'][0];
                }
            }
        }

        return [];
    }

    /**
     * @return stdClass|null
     */
    public function getUser(): ?stdClass
    {
        $userInfo = $this->getUserSocial();

        if (!empty($userInfo['id'])) {
            return $this->userService->getOauthUserData('vk', $userInfo['id']);
        }

        return null;
    }

    /**
     * @return stdClass|null
     */
    public function registerUser(): ?stdClass
    {
        $userInfo = $this->getUserSocial();

        if (isset($userInfo['response'][0]['id'])) {
            $userInfo = $userInfo['response'][0];


            if ($this->userService->checkSocialIdExists($userInfo['id'], AuthDictionary::VK_CLIENT_NAME)) {
                $errors[] = 'Такой email уже используется';
            }

            $insert = $this->userService->registerOauth($userInfo, AuthDictionary::VK_CLIENT_NAME);

            if ($insert) {
                return $this->userService->getOauthUserData('vk', $userInfo['id']);
            }
        }

        return null;
    }

}