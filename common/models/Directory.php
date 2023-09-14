<?php

namespace common\models;

use common\models\File;

class Directory
{
    /** @var string */
    private $name;

    /** @var string */
    private $baseUrl;

    /** @var string */
    private $path;

//    /** @var int */
//    private $isSecure;
//
//    /** @var int */
//    private $expirationTime;

    private int $createdAt;
    private int $updatedAt;
    private array $files;
}