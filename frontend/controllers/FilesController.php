<?php

namespace frontend\controllers;

use common\models\FileStorageItem;

class FilesController
{
    public function actionDownload(array $path)
    {
        $id = array_shift($path);

        /** @var FileStorageItem $model */
        $model = FileStorageItem::getModelById($id);

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

        FileStorageItem::downloadFile($id);
        return true;
    }

    private function downloadSecureFile($model)
    {
        $errors = [];
        if (!FileStorageItem::hasAccess($model)) {
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

    private function downloadExpirationTimeFile($model)
    {
        $errors = [];
        if (!FileStorageItem::isExpire($model)) {
            $errors[] = 'Время на скачивание файла истекло!';
            return $errors;
        }

        return $errors;
    }

}