<?php

use common\models\User;

class UserController
{
    public function actionLogin()
    {
        $email = '';
        $password = '';

        if (!empty($_POST['submit'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $errors = [];
            $userId = User::checkUserData($email, $password);

            if ($userId === false || !$this->isValidCaptcha()) {
                $errors[] = 'Неверные данные для входа на сайт';
            } else {
                User::auth($userId);
                header('Location: /cabinet');
            }
        }

        require_once(ROOT . '/frontend/views/user/login.php');
        return true;
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

        if (isset($_POST['submit'])) {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $errors = [];

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
     * @return bool
     */
    protected function isValidCaptcha(): bool
    {
        $result = $this->getCaptcha($_POST['g-recaptcha-response']);

        if ($result->success && $result->score > 0.5) {
            return true;
        }

        return false;
    }


}