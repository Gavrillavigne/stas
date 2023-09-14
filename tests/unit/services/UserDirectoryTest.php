<?php

namespace unit\services;

use PHPUnit\Framework\TestCase;
use common\models\User;
use services\DirectoryManagerService;

class UserDirectoryTest extends TestCase
{
    public function testMaxDirectoryCount()
    {
        $user = new User('username');
        $directoryManager = new DirectoryManagerService();

        // Для пользователя создано 19 каталогов
        $this->assertTrue($directoryManager->isDirectoryCountValid($user, 20));

        // Для пользователя создано 21 каталог
        $this->assertFalse($directoryManager->isDirectoryCountValid($user, 20));
    }
}