<?php

namespace dictionary;

class OAuth2Dictionary
{
    public static array $serviceClasses = [
        'vk' => 'VKontakteOAuth2Service',
        'mailru' => 'MailRuOAuth2Service'
    ];

}