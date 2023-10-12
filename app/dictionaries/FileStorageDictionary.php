<?php

namespace app\dictionaries;

class FileStorageDictionary
{
    public static $mimeTypes = [
        'pdf' => 'application/pdf',
        'txt' => 'text/plain',
        'rtf' => 'text/plain',
        'html' => 'text/html',
        'jpg' => 'image/jpeg',
    ];

    public static array $allowedFileTypes = ['txt', 'jpg', 'png', 'pdf', 'csv'];

}