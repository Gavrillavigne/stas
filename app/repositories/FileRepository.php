<?php

namespace app\repositories;

use infrastructure\components\Db;
use app\entities\File;
use stdClass;

class FileRepository
{
    private string $tableName = 'file_storage_item';

    /**
     * @param $id
     * @return bool|stdClass
     */
    public function getModelById($id): bool|stdClass
    {
        $db = Db::getConnection();
        $result = $db->query('SELECT * FROM ' . $this->tableName . ' WHERE id=' . $id);

        return $result->fetchObject();
    }

    /**
     * @param File $file
     * @return bool
     */
    public function uploadFile(File $file): bool
    {
        $userId = $file->getUserId();
        $baseUrl = $file->getBaseUrl();
        $path = $file->getPath();
        $extension = $file->getType();
        $fileName = $file->getName();
        $createdAt = $file->getCreatedAt();
        $isSecure = $file->isSecure();
        $expirationTime = $file->getExpirationTime();

        $db = Db::getConnection();
        $sql = 'INSERT INTO  ' . $this->tableName . ' (user_id, base_url, path, type, name, created_at, is_secure, expiration_time) VALUES (:user_id, :base_url, :path, :type, :name, :created_at, :is_secure, :expiration_time)';
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
            return false;
        }

        return true;
    }

    /**
     * @param $userId
     * @return array
     */
    public function getFileNamesByUserId($userId): array
    {
        $db = Db::getConnection();

        $files = [];
        $result = $db->query('SELECT * FROM ' . $this->tableName . ' WHERE user_id=' . $userId . ' ORDER BY id DESC');

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
     * @return bool
     */
    public function deleteFile($id): bool
    {
        $db = Db::getConnection();
        $result = $db->query('DELETE FROM ' . $this->tableName . ' WHERE id=' . $id);

        if (!$result->execute()) {
            return false;
        }

        return true;
    }

}