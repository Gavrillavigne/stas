<?php

namespace common\models;

use common\components\Db;
use stdClass;

class User
{
    /** @var string */
    private $name;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $email;

    /** @var string */
    private $password;

    /** @var int */
    private $status;

    /** @var int */
    private $createdAt;

    /** @var int */
    private $updatedAt;


    /**
     * TODO вынести в сервис
     * @return array
     */
    public function getDirectories(): array
    {
        $basePath = '/path/to/user/directory';
        $directories = [];

        if (is_dir($basePath)) {
            $contents = scandir($basePath);

            foreach ($contents as $item) {
                if ($item != '.' && $item != '..' && is_dir($basePath . '/' . $item)) {
                    // Здесь можно добавить дополнительную проверку на доступ пользователя к этой директории
                    $directories[] = $item;
                }
            }
        }

        return $directories;
    }

    /**
     * @param string $email
     * @param string $password
     * @return stdClass|null
     */
    public static function getUserData(string $email, string $password): ?stdClass
    {
        $db = Db::getConnection();
        $sql = 'SELECT * FROM user WHERE email = :email AND password = :password';

        $result = $db->prepare($sql);
        $result->bindParam(':email', $email, \PDO::PARAM_STR);
        $result->bindParam(':password', $password, \PDO::PARAM_STR);
        $result->execute();

        $user = $result->fetch(\PDO::FETCH_OBJ);
        if (!empty($user)) {
            return $user;
        }

        return null;
    }

    /**
     * @param string $oauthClient
     * @param int $oauthClientUserId
     * @return stdClass|null
     */
    public static function getOauthUserData(string $oauthClient, int $oauthClientUserId): ?stdClass
    {
        $db = Db::getConnection();
        $sql = 'SELECT * FROM user WHERE oauth_client = :oauthClient AND oauth_client_user_id = :oauthClientUserId';

        $result = $db->prepare($sql);
        $result->bindParam(':oauthClient', $oauthClient, \PDO::PARAM_STR);
        $result->bindParam(':oauthClientUserId', $oauthClientUserId, \PDO::PARAM_INT);
        $result->execute();

        $user = $result->fetch(\PDO::FETCH_OBJ);
        if (!empty($user)) {
            return $user;
        }

        return null;
    }

    /**
     * @param int $userId
     * @return void
     */
    public static function auth(int $userId): void
    {
        session_start();
        $_SESSION['user'] = $userId;
    }

    /**
     * @return mixed|void
     */
    public static function checkLogged()
    {
        if (!empty($_SESSION['user'])) {
            return $_SESSION['user'];
        }

        session_start();
    }

    /**
     * Проверяет имя: не меньше, чем 2 символа
     */
    public static function checkName($name)
    {
        if (strlen($name) >= 2) {
            return true;
        }
        return false;
    }

    /**
     * Проверяет имя: не меньше, чем 6 символов
     */
    public static function checkPassword($password)
    {
        if (strlen($password) >= 6) {
            return true;
        }
        return false;
    }

    /**
     * Проверяет email
     */
    public static function checkEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    public static function checkEmailExists($email)
    {
        $db = Db::getConnection();

        $sql = 'SELECT COUNT(*) FROM user WHERE email = :email';

        $result = $db->prepare($sql);
        $result->bindParam(':email', $email, \PDO::PARAM_STR);
        $result->execute();

        if ($result->fetchColumn()) {
            return true;
        }

        return false;
    }

    /**
     * @param $oauthClientUserId
     * @param $oauthClient
     * @return bool
     */
    public static function checkSocialIdExists($oauthClientUserId, $oauthClient): bool
    {
        $db = Db::getConnection();

        $sql = 'SELECT COUNT(*) FROM user WHERE oauth_client_user_id = :oauthClientUserId AND oauth_client = :oauthClient';

        $result = $db->prepare($sql);
        $result->bindParam(':oauthClientUserId', $oauthClientUserId, \PDO::PARAM_INT);
        $result->bindParam(':oauthClient', $oauthClient, \PDO::PARAM_STR);
        $result->execute();

        if ($result->fetchColumn()) {
            return true;
        }

        return false;
    }

    /**
     * @param $name
     * @param $email
     * @param $password
     * @return bool
     */
    public static function register($name, $email, $password): bool
    {
        $db = Db::getConnection();

        $sql = 'INSERT INTO user (name, email, password) '
            . 'VALUES (:name, :email, :password)';

        $result = $db->prepare($sql);
        $result->bindParam(':name', $name, \PDO::PARAM_STR);
        $result->bindParam(':email', $email, \PDO::PARAM_STR);
        $result->bindParam(':password', $password, \PDO::PARAM_STR);

        return $result->execute();
    }

    /**
     * @param array $oauthParams
     * @param string $oauthClientName
     * @return bool
     */
    public static function registerOauth(array $oauthParams, string $oauthClientName)
    {
        $db = Db::getConnection();

        $firstName = $oauthParams['first_name'];
        $lastName = $oauthParams['last_name'];
        $oauthClientUserId = $oauthParams['id'];

        $sql = 'INSERT INTO user (first_name, last_name, oauth_client, oauth_client_user_id) '
            . 'VALUES (:firstName, :lastName, :oauthClient, :oauthClientUserId)';

        $result = $db->prepare($sql);
        $result->bindParam(':firstName', $firstName, \PDO::PARAM_STR);
        $result->bindParam(':lastName', $lastName, \PDO::PARAM_STR);
        $result->bindParam(':oauthClient', $oauthClientName, \PDO::PARAM_STR);
        $result->bindParam(':oauthClientUserId', $oauthClientUserId, \PDO::PARAM_INT);

        return $result->execute();
    }

}