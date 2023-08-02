<?php

namespace dictionary;

class AuthDictionary
{
    const VK_CLIENT_NAME = 'vk';
    const MAILRU_CLIENT_NAME = 'mailru';

    public static array $classMap = [
        'default' => [
            'class' => 'services\\FormAuthService'
        ],
        self::VK_CLIENT_NAME => [
            'class' => 'services\\oauth\\VKontakteOAuth2Service'
        ],
        self::MAILRU_CLIENT_NAME => [
            'class' => 'services\\oauth\\MailRuOAuth2Service'
        ]
    ];

}