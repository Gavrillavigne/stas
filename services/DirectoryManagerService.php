<?php

namespace services;

use common\models\User;

class DirectoryManagerService
{
    /**
     * @param User $user
     * @param int $maxDirectoryCount
     * @return bool
     */
    public function isDirectoryCountValid(User $user, int $maxDirectoryCount): bool
    {
        $userDirectories = $user->getDirectories();

        return count($userDirectories) <= $maxDirectoryCount;
    }

}