<?php

namespace app\helpers;

use app\entities\User;
use stdClass;

class UserEntityConverter
{
    /**
     * @param stdClass $user
     * @return User
     */
    public static function fromStdToEntity(stdClass $user): User
    {
        return new User(
            $user->id,
            $user->name,
            $user->first_name,
            $user->last_name,
            $user->email,
            $user->password,
            $user->status,
            $user->oauth_client,
            $user->oauth_client_user_id,
            $user->created_at,
            $user->update_at
        );
    }

}