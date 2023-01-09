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

            $errors = '';

            $userId = User::checkUserData($email, $password);

            if ($userId === false) {
                $errors = 'Неверные данные для входа на сайт';
                echo $errors;
            } else {
                User::auth($userId);
                header('Location: /cabinet');
            }

            return true;
        }

        require_once(ROOT . '/frontend/views/user/login.php');
        return true;
    }

    public function actionLogout()
    {

    }

    public function actionRegister()
    {

    }

}