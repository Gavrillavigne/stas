<?php

namespace app\dictionaries;

class AuthDictionary
{
    const VK_CLIENT_NAME = 'vk';
    const MAILRU_CLIENT_NAME = 'mailru';

    public static array $classMap = [
        'default' => [
            'class' => 'app\\services\\FormAuthService'
        ],
        self::VK_CLIENT_NAME => [
            'class' => 'app\\services\\oauth\\VKontakteOAuth2Service'
        ],
        self::MAILRU_CLIENT_NAME => [
            'class' => 'app\\services\\oauth\\MailRuOAuth2Service'
        ]
    ];

}