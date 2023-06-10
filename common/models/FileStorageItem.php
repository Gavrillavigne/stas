<?php

namespace common\models;

use common\components\Db;
use stdClass;
use dictionary\FileStorageDictionary;

class FileStorageItem
{
    const UPLOADS_DIR = 'uploads/';

    public static string $tableName = 'file_storage_item';

    /**
     * @param $model
     * @return bool
     */
    public static function hasAccess($model): bool
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
    public static function isExpire($model): bool
    {
        return !empty($model->expiration_time) && time() < $model->expiration_time;
    }

    /**
     * @param $userId
     * @return array
     */
    public static function uploadFile($userId = null): array
    {
        $errors = [];
        $secureDir = '';
        $isSecure = 0;

        if (empty($_FILES)) {
            return $errors;
        }

        if (!empty($_FILES) && $_FILES["filename"]["error"] != UPLOAD_ERR_OK) {
            $errors[] = 'Код ошибки при загрузке файла: ' . $_FILES["filename"]["error"];
            return $errors;
        }

        if ($_POST['secure'] == 1) {
            $secureDir = "secure/$userId/";
            if (!self::checkSecureDir($userId)) {
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
        $path = $baseUrl . $fileName . '.' . $extension;
        $createdAt = time();
        $expirationTime = $_POST['expiration'] == 1 ? time() + 36000 : null;
        $isUpload = move_uploaded_file($_FILES["filename"]["tmp_name"], $path);

        if (!$isUpload) {
            $errors[] = 'Ошибка загрузки на диск! Путь загрузки: ' . $path;
            return $errors;
        }

        $db = Db::getConnection();
        $sql = 'INSERT INTO  ' . self::$tableName . ' (user_id, base_url, path, type, name, created_at, is_secure, expiration_time) VALUES (:user_id, :base_url, :path, :type, :name, :created_at, :is_secure, :expiration_time)';
        $result = $db->prepare($sql);
        $result->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $result->bindParam(':base_url', $baseUrl, \PDO::PARAM_STR);
        $result->bindParam(':path', $path, \PDO::PARAM_STR);
        $result->bindParam(':type', $extension, \PDO::PARAM_STR);
        $result->bindParam(':name', $fileName, \PDO::PARAM_STR);
        $result->bindParam(':created_at', $createdAt, \PDO::PARAM_INT);
        $result->bindParam(':is_secure', $isSecure, \PDO::PARAM_INT);
        $result->bindParam(':expiration_time', $expirationTime, \PDO::PARAM_INT);

        if (!$result->execute()) {
            $errors[] = 'Ошибка при вставке записи в БД!';
        }

        return $errors;
    }

    /**
     * @param $userId
     * @return bool
     */
    private static function checkSecureDir($userId): bool
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
    public static function getFileNamesByUserId(int $userId = null): array
    {
        $db = Db::getConnection();

        $files = [];
        $result = $db->query('SELECT * FROM ' . self::$tableName . ' WHERE user_id=' . $userId . ' ORDER BY id DESC');

        while ($row = $result->fetch()) {
            $files[$row['id']]['fileName'] = $row['name'] . '.' . $row['type'];
            $files[$row['id']]['filePath'] = $row['base_url'] . $row['name'] . '.' . $row['type'];
            $files[$row['id']]['isSecure'] = $row['is_secure'];
            $files[$row['id']]['expirationTime'] = $row['expiration_time'];
        }

        return $files;
    }

    /**
     * @param $id
     * @return array
     */
    public static function deleteFile($id): array
    {
        $errors = [];
        $db = Db::getConnection();
        $model = self::getModelById($id);

        if (!empty($model)) {
            $filePath = $model->path ?? '';
            $result = $db->query('DELETE FROM ' . self::$tableName . ' WHERE id=' . $id);

            if ($result->execute()) {
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
     * @return bool|stdClass
     */
    public static function getModelById($id): bool|stdClass
    {
        $db = Db::getConnection();
        $result = $db->query('SELECT * FROM ' . self::$tableName . ' WHERE id=' . $id);

        return $result->fetchObject();
    }

    /**
     * @param $id
     * @return array
     */
    public static function downloadFile($id): array
    {
        $errors = [];
        $model = self::getModelById($id);

        if (!self::hasAccess($model)) {
            header('Location: /user/login');
        }

        if (!self::isExpire($model)) {
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
    public static function openFile(array $params): string|false
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