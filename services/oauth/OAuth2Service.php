<?php

namespace services\oauth;

abstract class OAuth2Service
{
    protected string $redirectUri; // Адрес сайта
    protected $clientId; // ID приложения
    protected $clientSecret; // Защищённый ключ
    protected string $apiUrl;
    protected $params;

    /**
     * Когда фабричный метод используется внутри бизнес-логики Создателя,
     * подклассы могут изменять логику косвенно, возвращая из фабричного метода
     * различные типы коннекторов.
     * @param $redirectUrl
     */
    public function __construct($redirectUrl)
    {
        $this->setParams($redirectUrl);
    }

    /**
     * Фабричный метод.
     */
    public static function initial($service, $redirectUrl)
    {
        $service = 'services\\oauth\\' . $service;
        return new $service($redirectUrl);
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