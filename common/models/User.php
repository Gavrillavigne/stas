<?php

namespace common\models;

use common\components\Db;

class User
{
    /**
     * @param string $email
     * @param string $password
     * @return bool|int
     */
    public static function checkUserData(string $email, string $password): bool|int
    {
        $db = Db::getConnection();
        $sql = 'SELECT * FROM user WHERE email = :email AND password = :password';

        $result = $db->prepare($sql);
        $result->bindParam(':email', $email, \PDO::PARAM_STR);
        $result->bindParam(':password', $password, \PDO::PARAM_STR);
        $result->execute();

        $user = $result->fetch(\PDO::FETCH_ASSOC);
        if (!empty($user)) {
            return $user['id'];
        }

        return false;
    }

    /**
     * @param string $oauthClient
     * @param int $oauthClientUserId
     * @return bool|int
     */
    public static function checkOauthUserData(string $oauthClient, int $oauthClientUserId): bool|int
    {
        $db = Db::getConnection();
        $sql = 'SELECT * FROM user WHERE oauth_client = :oauthClient AND oauth_client_user_id = :oauthClientUserId';

        $result = $db->prepare($sql);
        $result->bindParam(':oauthClient', $oauthClient, \PDO::PARAM_STR);
        $result->bindParam(':oauthClientUserId', $oauthClientUserId, \PDO::PARAM_INT);
        $result->execute();

        $user = $result->fetch(\PDO::FETCH_ASSOC);
        if (!empty($user)) {
            return $user['id'];
        }

        return false;
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
     * @param $name
     * @param $email
     * @param $password
     * @return mixed
     */
    public static function register($name, $email, $password)
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
     * @return bool
     */
    public static function registerOauth(array $oauthParams)
    {
        $db = Db::getConnection();

        $firstName = $oauthParams['first_name'];
        $lastName = $oauthParams['last_name'];
        $oauthClient = 'vk';
        $oauthClientUserId = $oauthParams['id'];

        $sql = 'INSERT INTO user (first_name, last_name, oauth_client, oauth_client_user_id) '
            . 'VALUES (:firstName, :lastName, :oauthClient, :oauthClientUserId)';

        $result = $db->prepare($sql);
        $result->bindParam(':firstName', $firstName, \PDO::PARAM_STR);
        $result->bindParam(':lastName', $lastName, \PDO::PARAM_STR);
        $result->bindParam(':oauthClient', $oauthClient, \PDO::PARAM_STR);
        $result->bindParam(':oauthClientUserId', $oauthClientUserId, \PDO::PARAM_INT);

        return $result->execute();
    }

}