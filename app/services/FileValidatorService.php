<?php

namespace app\services;

use app\dictionaries\FileStorageDictionary;

class FileValidatorService
{
    /**
     * @param string $filePath
     * @return bool
     */
    public function isValidFileType(string $filePath): bool
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        return in_array($extension, FileStorageDictionary::$allowedFileTypes);
    }

    /**
     * @param string $filePath
     * @return bool
     */
    public function isValidFileSize(string $filePath): bool
    {
        $fileSize = filesize($filePath);
        $fileSizeMB = round($fileSize / (1024 * 1024), 2);

        return $fileSizeMB <= 10;
    }

}