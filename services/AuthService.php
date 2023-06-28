<?php

namespace services;

use services\IAuthService;

class AuthService
{
    private $service;

    public function __construct(IAuthService $service)
    {
        $this->service = $service;
    }

    public function getUser()
    {
        $this->service->getUser();
    }

}