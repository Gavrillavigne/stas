<?php

namespace services\oauth;

abstract class OAuth2Service
{
    /** @var string */
    protected string $redirectUri; // Адрес сайта

    /** @var string */
    protected $clientId; // ID приложения

    /** @var string */
    protected $clientSecret; // Защищённый ключ

    /** @var string */
    protected $apiUrl;

    /** @var array */
    protected $params;

    /**
     * Когда фабричный метод используется внутри бизнес-логики Создателя,
     * подклассы могут изменять логику косвенно, возвращая из фабричного метода
     * различные типы коннекторов.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $redirectUrl = ($params['action'] ?? '') . '/';

        $this->setParams($redirectUrl);
    }

    /**
     * @param string $redirectUrl
     * @return void
     */
    abstract protected function setParams(string $redirectUrl): void;
    abstract public function getCode();

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->apiUrl . '?' . urldecode(http_build_query($this->getParams()));
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}