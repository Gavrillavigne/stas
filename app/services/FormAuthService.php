<?php

namespace app\services;

use app\entities\User;
use stdClass;
use app\services\UserService;

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

    /** @var UserService */
    public $userService;

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->name = $_POST['name'] ?? '';
        $this->email = $_POST['email'] ?? '';
        $this->password = $_POST['password'] ?? '';
        $this->hasSubmit = !empty($_POST['submit']);
        $this->userService = new UserService();
    }

    /**
     * @return stdClass|null
     */
    public function getUser(): ?User
    {
        if ($this->hasSubmit) {
            return $this->userService->getUserData($this->email, $this->password);
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
            if (!$this->userService->checkName($this->name)) {
                $errors[] = 'Имя не должно быть короче 2-х символов';
            }

            if (!$this->userService->checkEmail($this->email)) {
                $errors[] = 'Неправильный email';
            }

            if (!$this->userService->checkPassword($this->password)) {
                $errors[] = 'Пароль не должен быть короче 6-ти символов';
            }

            if ($this->userService->checkEmailExists($this->email)) {
                $errors[] = 'Такой email уже используется';
            }

            if ($this->userService->register($this->name, $this->email, $this->password) && empty($errors)) {
                return $this->userService->getUserData($this->email, $this->password);
            }
        }

        return null;
    }

}