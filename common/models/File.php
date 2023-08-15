<?php

namespace common\models;

class File
{
    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var string */
    private $baseUrl;

    /** @var string */
    private $path;

    /** @var int */
    private $isSecure;

    /** @var int */
    private $expirationTime;

    /** @var int */
    private $createdAt;

    /** @var int */
    private $updatedAt;
}