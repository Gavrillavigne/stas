<?php

namespace app\services;

use app\entities\User;
use stdClass;

interface IAuthService
{
    /**
     * @return stdClass|null
     */
    public function getUser(): ?User;

    public function registerUser(): ?stdClass;
}