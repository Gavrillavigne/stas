<?php

namespace frontend\controllers;

use common\models\User;
use services\oauth\OAuth2Service;
use dictionary\OAuth2Dictionary;

class UserController
{
    /**
     * @param $params
     * @return OAuth2Service|null
     */
    private function getOAuth2Service($params): ?OAuth2Service
    {
        $redirectUrl = ($params[0] ?? '') . '/';
        preg_match("/&state=(.*)/", $params[1], $services);
        $service = $services[1] ?? '';

        if (empty($service)) {
            $service = $params[1] ?? '';
        }

        return OAuth2Service::initial(OAuth2Dictionary::$serviceClasses[$service], $redirectUrl);
    }

    public function actionOAuth2Login($params)
    {
        $oauthService = $this->getOAuth2Service($params);
        $code = $oauthService->getCode();
        if (!empty($code)) {
            $oauthParams = $oauthService->getTokenParams($code);
            $token = json_decode(file_get_contents('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($oauthParams))), true);

            if (!empty($token['access_token'])) {
                $params = $oauthService->getUserParams($token);
                $userInfo = json_decode(file_get_contents('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query($params))), true);

                if (isset($userInfo['response'][0]['id'])) {
                    $userInfo = $userInfo['response'][0];
                    $userId = User::checkOauthUserData('vk', $userInfo['id']);
                }
            }

            if (!$this->redirectToCabinet($userId, true)) {
                $errors[] = 'Неверные данные для входа через социальную сеть';
            }
        }
    }

    public function actionLogin()
    {
        $email = '';
        $password = '';
        $errors = [];

        // Обычная авторизация
        if (!empty($_POST['submit'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $userId = User::checkUserData($email, $password);

            if (!$this->redirectToCabinet($userId)) {
                $errors[] = 'Неверные данные для входа на сайт';
            }
        }

        require_once(ROOT . '/frontend/views/user/login.php');
        return true;
    }

    /**
     * @param $userId
     * @param bool $isOauth
     * @return bool
     */
    private function redirectToCabinet($userId, bool $isOauth = false)
    {
        if (!empty($userId) && $this->isValidCaptcha($isOauth)) {
            User::auth($userId);
            header('Location: /cabinet');
            return true;
        } else {
            return false;
        }
    }

    public function actionLogout()
    {

    }

    /**
     * @return true
     */
    public function actionRegister()
    {
        $result = false;
        $errors = [];
        $oauthService = (new VKontakteOAuth2Service('register/'));

        // Регистрация через соц. сети
        if (!empty($_GET['code'])) {
            $oauthParams = $oauthService->getTokenParams($_GET['code']);
            $token = json_decode(file_get_contents('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($oauthParams))), true);

            if (!empty($token['access_token'])) {
                $params = $oauthService->getUserParams($token);
                $userInfo = json_decode(file_get_contents('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query($params))), true);

                if (isset($userInfo['response'][0]['id'])) {
                    $userInfo = $userInfo['response'][0];
                    $result = User::registerOauth($userInfo);
                    $userId = User::checkOauthUserData('vk', $userInfo['id']);
                }
            }

            if (!$this->redirectToCabinet($userId)) {
                $errors[] = 'Неверные данные для регистрации через социальную сеть';
            }
        }

        if (isset($_POST['submit'])) {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (!User::checkName($name)) {
                $errors[] = 'Имя не должно быть короче 2-х символов';
            }

            if (!User::checkEmail($email)) {
                $errors[] = 'Неправильный email';
            }

            if (!User::checkPassword($password)) {
                $errors[] = 'Пароль не должен быть короче 6-ти символов';
            }

            if (User::checkEmailExists($email)) {
                $errors[] = 'Такой email уже используется';
            }

            if (empty($errors) && $this->isValidCaptcha()) {
                $result = User::register($name, $email, $password);
                $userId = User::checkUserData($email, $password);
                if ($userId) {
                    User::auth($userId);
                    header('Location: /cabinet');
                }
            }
        }

        require_once(ROOT . '/frontend/views/user/register.php');
        return true;
    }

    /**
     * Делает запрос на google сервис
     * @param $secretKey
     * @return mixed
     */
    protected function getCaptcha($secretKey)
    {
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . getenv('RECAPTCHA3_SECRET_KEY') . "&response={$secretKey}");

        return json_decode($response);
    }

    /**
     * @param bool $isOauth
     * @return bool
     */
    protected function isValidCaptcha(bool $isOauth = false): bool
    {
        $result = $this->getCaptcha($_POST['g-recaptcha-response']);

        if (($result->success && $result->score > 0.5) || $isOauth) {
            return true;
        }

        return false;
    }


}