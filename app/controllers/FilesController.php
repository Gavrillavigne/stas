<?php

namespace app\controllers;

use app\services\FileService;
use stdClass;

class FilesController
{
    /** @var FileService */
    public $fileService;

    public function __construct()
    {
        $this->fileService = new FileService();
    }

    /**
     * @param array $path
     * @return array|true
     */
    public function actionDownload(array $path)
    {
        $id = array_shift($path);

        /** @var bool|stdClass $model */
        $model = $this->fileService->getModelById($id);

        if ($model->is_secure) {
            $errors = $this->downloadSecureFile($model);

            if (!empty($errors)) {
                return $errors;
            }
        }

        if (!empty($model->expiration_time)) {
            $errors = $this->downloadExpirationTimeFile($model);

            if (!empty($errors)) {
                return $errors;
            }
        }

        $this->fileService->downloadFile($id);
        return true;
    }

    /**
     * @param $model
     * @return array
     */
    private function downloadSecureFile($model)
    {
        $errors = [];
        if (!$this->fileService->hasAccess($model)) {
            $errors[] = 'У вас нет прав на скачивание файла!';
            return $errors;
        }

        if (!empty($model->expiration_time)) {
            $errors = $this->downloadExpirationTimeFile($model);

            if (!empty($errors)) {
                return $errors;
            }
        }

        $filePath = '/' . $model->path;
        //заголовок для внутреннего редиректа
        header("X-Accel-Redirect: " . $filePath);
        //возвращаем Content-Type, чтобы браузер мог корректно обработать файл
        header('Content-Type: ' . mime_content_type($filePath));

        return $errors;
    }

    /**
     * @param $model
     * @return array
     */
    private function downloadExpirationTimeFile($model)
    {
        $errors = [];
        if (!$this->fileService->isExpire($model)) {
            $errors[] = 'Время на скачивание файла истекло!';
            return $errors;
        }

        return $errors;
    }

}