<?php

namespace services;

use common\models\User;
use stdClass;

class FormAuthService implements IAuthService
{
    /** @var string */
    private $name;

    /** @var string */
    private $email;

    /** @var string */
    private $password;

    /** @var bool */
    private $hasSubmit;

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->name = $_POST['name'] ?? '';
        $this->email = $_POST['email'] ?? '';
        $this->password = $_POST['password'] ?? '';
        $this->hasSubmit = !empty($_POST['submit']);
    }

    /**
     * @return stdClass|null
     */
    public function getUser(): ?stdClass
    {
        if ($this->hasSubmit) {
            return User::getUserData($this->email, $this->password);
        }

        return null;
    }

    /**
     * @return stdClass|null
     */
    public function registerUser(): ?stdClass
    {
        $errors = [];
        if ($this->hasSubmit) {
            if (!User::checkName($this->name)) {
                $errors[] = 'Имя не должно быть короче 2-х символов';
            }

            if (!User::checkEmail($this->email)) {
                $errors[] = 'Неправильный email';
            }

            if (!User::checkPassword($this->password)) {
                $errors[] = 'Пароль не должен быть короче 6-ти символов';
            }

            if (User::checkEmailExists($this->email)) {
                $errors[] = 'Такой email уже используется';
            }

            if (User::register($this->name, $this->email, $this->password) && empty($errors)) {
                return User::getUserData($this->email, $this->password);
            }
        }

        return null;
    }

}