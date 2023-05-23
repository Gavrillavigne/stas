<?php

use common\models\FileStorageItem;
use common\models\User;

class FilesController
{
    public function actionDownload(array $path)
    {
        $user = $path[3] ?? null;
        $id = array_shift($path);
        $currentUser = User::checkLogged() ?? null;

        if ($user != $currentUser && !empty($user) && !empty($currentUser)) {
            header('Location: /user/login');
            return true;
        }

        $filePath = '/' . implode('/', $path);
        //заголовок для внутреннего редиректа
        header("X-Accel-Redirect: " . $filePath);
        //возвращаем Content-Type, чтобы браузер мог корректно обработать файл
        header('Content-Type: ' . mime_content_type($filePath));
        FileStorageItem::downloadFile($id);
        return true;
    }

}