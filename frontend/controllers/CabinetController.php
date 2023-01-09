<?php

use common\models\FileStorageItem;
use common\models\User;

class CabinetController
{
    private $userId;

    public function __construct()
    {
        $this->userId = User::checkLogged() ?? null;
    }

    public function actionIndex()
    {
        $errors = [];

        if (!empty($_POST['delete'])) {
            $errors = FileStorageItem::deleteFile($_POST['delete']);
        }

        if (!empty($_POST['download'])) {
            $errors = FileStorageItem::downloadFile($_POST['download']);
        }

        if (!empty($_FILES)) {
            $errors = array_merge($errors, FileStorageItem::uploadFile($this->userId));
        }

        $this->renderCabinet($errors);
    }

    /**
     * @return true
     */
    private function renderCabinet($errors = [])
    {
        if (empty($this->userId)) {
            header('Location: /user/login');
        }

        $files = FileStorageItem::getFileNamesByUserId($this->userId);

        require_once(ROOT . '/frontend/views/cabinet/index.php');
        return true;
    }

}