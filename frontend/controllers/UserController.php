<?php

namespace frontend\controllers;

use common\models\User;
use services\IAuthService;
use services\AuthService;

class UserController
{
    /**
     * @param $params
     * @return true
     */
    public function actionLogin($params)
    {
        $errors = [];
        $params['action'] = 'login';

        /** @var IAuthService $authService */
        $authService = new AuthService($params);
        $user = $authService->getUser();

        if (!$this->redirectToCabinet($user->id, !empty($user->oauth_client_user_id)) && !empty($_POST)) {
            $errors[] = 'Неверные данные для входа на сайт';
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
    public function actionRegister($params)
    {
        $errors = [];
        $params['action'] = 'register';

        /** @var IAuthService $authService */
        $authService = new AuthService($params);
        $user = $authService->getUser();

        if (!$this->redirectToCabinet($user->id, !empty($user->oauth_client_user_id)) && !empty($_POST)) {
            $errors[] = 'Неверные данные для входа на сайт';
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