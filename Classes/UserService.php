<?php
/**
 * 11.12.2023
 * 13:33
 * Prepared by Buğra Şıkel @bugraskl
 * https://www.bugra.work/
 */

namespace UserService;

class UserService
{
    public function getUserInfo($userId)
    {
        global $db;
        $user = $db->from('users')->where('id', $userId)->first();
        if ($user) {
            return $user;
        }else {
            return false;
        }
    }
}