<?php

namespace app\controllers;

use app\services\{UserService, FileService};

class CabinetController
{
    private $userId;

    /** @var UserService */
    public $userService;

    /** @var FileService */
    public $fileService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->fileService = new FileService();
        $this->userId = $this->userService->checkLogged() ?? null;
    }

    public function actionIndex()
    {
        $errors = [];

        if (!empty($_POST['delete'])) {
            $errors = $this->fileService->deleteFile($_POST['delete']);
        }

        if (!empty($_POST['download'])) {
            $errors = $this->fileService->downloadFile($_POST['download']);
        }

        if (!empty($_FILES)) {
            $errors = array_merge($errors, $this->fileService->uploadFile($this->userId));
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

        $files = $this->fileService->getFileNamesByUserId($this->userId);

        require_once(ROOT . '/public/views/cabinet/index.php');
        return true;
    }

    /**
     * @param array $params
     * @return bool
     */
    public function actionOpenFile(array $params): bool
    {
        if (!empty($params)) {
            $result = $this->fileService->openFile($params);

            if ($result !== false) {
                echo $result;
                return true;
            }
        }

        echo 'Error: Empty file path or File doesn\'t exist !';
        return true;
    }

}