<?php

namespace services;

use stdClass;
interface IAuthService
{
    /**
     * @return stdClass|null
     */
    public function getUser(): ?stdClass;

    public function registerUser(): ?stdClass;
}