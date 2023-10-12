<?php

namespace app\services;

class DirectoryValidatorService
{
    /**
     * @param $directoryPath
     * @param $maxFileCount
     * @return bool
     */
    public function isFileCountValid($directoryPath, $maxFileCount): bool
    {
        $files = scandir($directoryPath);
        $fileCount = count($files) - 2;

        return $fileCount <= $maxFileCount;
    }

    /**
     * @param $directoryPath
     * @return bool
     */
    public function hasNoDuplicateFileNames($directoryPath): bool
    {
        $files = scandir($directoryPath);
        $fileNames = [];

        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                if (in_array($file, $fileNames)) {
                    return false;
                }
                $fileNames[] = $file;
            }
        }

        return true;
    }
}