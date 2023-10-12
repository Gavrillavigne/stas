<?php

namespace app\services;

use app\dictionaries\AuthDictionary;
use app\entities\User;
use stdClass;

class AuthService
{
    /** @var IAuthService  */
    private $service;

    /** @var array  */
    private $params;

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
        $this->service = $this->getService();
    }

    /**
     * @return IAuthService|null
     */
    private function getService(): ?IAuthService
    {
        $service = null;

        $serviceName = $this->getServiceName();

        if (!empty(AuthDictionary::$classMap[$serviceName])) {
            $service = new AuthDictionary::$classMap[$serviceName]['class']($this->params);
        }

        return $service;
    }

    /**
     * @return string
     */
    private function getServiceName(): string
    {
        return !empty($this->params[0]) ? $this->params[0] : 'default';
    }

    /**
     * @return stdClass|null
     */
    public function getUser(): ?User
    {
        if ($this->params['action'] == 'register') {
            return $this->service->registerUser();
        }

        return $this->service->getUser();
    }

}