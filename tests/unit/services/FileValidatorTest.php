<?php

namespace unit\services;

use app\services\FileValidatorService;
use PHPUnit\Framework\TestCase;

class FileValidatorTest extends TestCase
{
    public function testValidFileTypes()
    {
        $validator = new FileValidatorService();

        // Test valid file types
        $this->assertTrue($validator->isValidFileType('file.txt'));
        $this->assertTrue($validator->isValidFileType('image.jpg'));
        $this->assertTrue($validator->isValidFileType('image.png'));
        $this->assertTrue($validator->isValidFileType('document.pdf'));
        $this->assertTrue($validator->isValidFileType('data.csv'));
    }

    public function testInvalidFileTypes()
    {
        $validator = new FileValidatorService();

        // Test invalid file types
        $this->assertFalse($validator->isValidFileType('script.php'));
        $this->assertFalse($validator->isValidFileType('spreadsheet.xlsx'));
    }

    public function testValidFileSize()
    {
        $validator = new FileValidatorService();

        $this->assertTrue($validator->isValidFileSize('large.txt')); // 10MB
    }

    public function testInvalidFileSize()
    {
        $validator = new FileValidatorService();

        $this->assertFalse($validator->isValidFileSize('small.jpg')); // 10MB
    }

}