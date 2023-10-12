<?php

namespace app\services;

use app\dictionaries\FileStorageDictionary;
use app\entities\File;
use app\repositories\FileRepository;
use infrastructure\components\Db;
use stdClass;
use app\services\FileValidatorService;

class FileService
{
    const UPLOADS_DIR = 'infrastructure/storage/uploads/';

    /** @var FileRepository */
    public $repository;

    /** @var FileValidatorService  */
    public $fileValidatorService;

    public function __construct()
    {
        $this->repository = new FileRepository();
        $this->fileValidatorService = new FileValidatorService();
    }

    /**
     * @param $model
     * @return bool
     */
    public function hasAccess($model): bool
    {
        if (!empty($model)) {
            $fileOwner = $model->user_id;
            $currentUser = $_SESSION['user'] ?? null;

            return $fileOwner == $currentUser;
        }

        return false;
    }

    /**
     * @param $model
     * @return bool
     */
    public function isExpire($model): bool
    {
        return !empty($model->expiration_time) && time() < $model->expiration_time;
    }

    /**
     * @param $userId
     * @return array
     */
    public function uploadFile($userId = null): array
    {
        $errors = [];
        $secureDir = '';
        $isSecure = 0;

        if (empty($_FILES)) {
            $errors[] = 'Файл не выбран!';
            return $errors;
        }

        if (!empty($_FILES) && $_FILES["filename"]["error"] != UPLOAD_ERR_OK) {
            $errors[] = 'Код ошибки при загрузке файла: ' . $_FILES["filename"]["error"];
            return $errors;
        }

        if ($_POST['secure'] == 1) {
            $secureDir = "secure/$userId/";
            if (!$this->checkSecureDir($userId)) {
                $errors[] = 'Ошибка создания папки на диске! Путь к директории: ' . $secureDir;
                return $errors;
            }
            $isSecure = 1;
        }

        $baseUrl = self::UPLOADS_DIR . $secureDir;
        $fileName = $_FILES["filename"]["name"];
        $parts = explode('.', $fileName);
        $extension = array_pop($parts);
        $fileName = implode($parts);
        $fileName = $fileName . '(' . date('Y-m-d H:i:s') . ')';
        $path = ROOT . '/' . $baseUrl . $fileName . '.' . $extension;
        $createdAt = time();
        $expirationTime = $_POST['expiration'] == 1 ? time() + 36000 : null;

        if (!$this->fileValidatorService->isValidFileType($path)) {
            $errors[] = 'Запрещенный для загрузки тип файла! Тип: ' . $extension;
            return $errors;
        }

        if (!$this->fileValidatorService->isValidFileSize($_FILES["filename"]["tmp_name"])) {
            $errors[] = 'Размер файла превышает допустимое значение! Максимальный размер файла: 10 Мб';
            return $errors;
        }

        $isUpload = move_uploaded_file($_FILES["filename"]["tmp_name"], $path);

        if (!$isUpload) {
            $errors[] = 'Ошибка загрузки на диск! Путь загрузки: ' . $path;
            return $errors;
        }

        $file = new File(null, $userId, $baseUrl, $path, $extension, $fileName, $isSecure, $expirationTime, $createdAt, null);

        if (!$this->repository->uploadFile($file)) {
            $errors = 'Ошибка при вставке записи в БД!';
        }

        return $errors;
    }

    /**
     * @param $userId
     * @return bool
     */
    public function checkSecureDir($userId): bool
    {
        $filename = self::UPLOADS_DIR . 'secure/' . $userId;

        if (is_dir($filename)) {
            return true;
        }

        mkdir($filename, 0775);
        return is_dir($filename);
    }

    /**
     * @param int|null $userId
     * @return array
     */
    public function getFileNamesByUserId(int $userId = null): array
    {
        return $this->repository->getFileNamesByUserId($userId);
    }

    /**
     * @param $id
     * @return array
     */
    public function deleteFile($id): array
    {
        $errors = [];
        $model = $this->repository->getModelById($id);

        if (!empty($model)) {
            $filePath = $model->path ?? '';

            if ($this->repository->deleteFile($model->id)) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                } else {
                    $errors[] = 'Ошибка удаления файла с диска!';
                }
            } else {
                $errors[] = 'Ошибка удаления файла из БД!';
            }

            return $errors;
        }

        $errors[] = 'Файл не найден в БД!';

        return $errors;
    }

    /**
     * @param $id
     * @return array
     */
    public function downloadFile($id): array
    {
        $errors = [];
        $model = $this->repository->getModelById($id);

        if (!$this->hasAccess($model)) {
            header('Location: /user/login');
        }

        if (!$this->isExpire($model)) {
            $errors[] = 'Время на скачивание файла истекло!';
            return $errors;
        }

        if (!empty($model)) {
            $filePath = $model->path ?? '';
            header("Content-Type: image/png");
            header("Content-Length: " . filesize($filePath));
            $quoted = sprintf('"%s"', addcslashes(basename($filePath), '"\\'));
            header("Content-Disposition: attachment; filename=$quoted");
            $fp = fopen($filePath, 'rb');
            fpassthru($fp);

            return $errors;
        }

        $errors[] = 'Файл не найден в БД!';
        return $errors;
    }

    /**
     * @param array $params
     * @return string|false
     */
    public function openFile(array $params): string|false
    {
        $filePath = implode('/', $params);
        $filePath = urldecode($filePath);
        $fileContent = file_get_contents(ROOT . '/' . $filePath);

        if (!empty($fileContent)) {
            $fileName = array_pop($params);
            $parts = explode('.', $fileName);
            $extension = array_pop($parts);
            $mimeType = FileStorageDictionary::$mimeTypes[$extension];

            if (empty($mimeType)) {
                return false;
            }
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . strlen($fileContent));
            header('Content-disposition: inline; filename="' . $fileName . '"');
            header('Cache-Control: public, must-revalidate, max-age=0');
            header('Pragma: public');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        }

        return $fileContent;
    }

}