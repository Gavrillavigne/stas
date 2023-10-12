<?php

namespace unit\services;

use app\services\DirectoryValidatorService;
use PHPUnit\Framework\TestCase;

class DirectoryValidatorTest extends TestCase
{
    public function testMaxFileCount()
    {
        $validator = new DirectoryValidatorService();

        // Каталог с 9 файлами
        $this->assertTrue($validator->isFileCountValid('/path/to/directory', 10));

        // Каталог с 11 файлами
        $this->assertFalse($validator->isFileCountValid('/path/to/directory', 10));
    }

    public function testNoDuplicateFileNames()
    {
        $validator = new DirectoryValidatorService();

        // Каталог без дубликатов имен файлов
        $this->assertTrue($validator->hasNoDuplicateFileNames('/path/to/directory'));

        // Каталог с дубликатами имен файлов
        $this->assertFalse($validator->hasNoDuplicateFileNames('/path/to/directory'));
    }
}