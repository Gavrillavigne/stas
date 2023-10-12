<?php

namespace app\repositories;

use infrastructure\components\Db;
use stdClass;

class UserRepository
{
    /**
     * @param string $email
     * @param string $password
     * @return stdClass|null
     */
    public function getUserByEmailAndPassword(string $email, string $password): ?stdClass
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

}