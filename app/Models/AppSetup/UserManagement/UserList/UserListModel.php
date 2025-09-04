<?php

namespace App\Models\AppSetup\UserManagement\UserList;

use CodeIgniter\Model;

class UserListModel extends Model
{
    protected $table            = 'user_auth';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function checkUser($user_name, $user_id = null)
    {
        if ($user_id !== null || $user_id !== '') {
            return $this->select('user_name')
                ->where('user_id <>', $user_id)
                ->where('user_name', $user_name)
                ->first();
        }

        return $this->select('user_name')
            ->where('user_name', $user_name)
            ->first();
    }

    public function checkUserPhone(string $phone, $user_id = null)
    {
        if ($user_id !== null) {
            return $this->select('phone_hash')
                ->where('user_id <>', $user_id)
                ->where('phone_hash', $phone)
                ->first();
        }

        return $this->select('phone_hash')
            ->where('phone_hash', $phone)
            ->first();
    }

    public function checkUserEmail($email_address, $user_id = null)
    {
        if ($user_id !== null) {
            return $this->select('email_hash')
                ->where('user_id <>', $user_id)
                ->where('email_hash', $email_address)
                ->first();
        }

        return $this->select('email_hash')
            ->where('email_hash', $email_address)
            ->first();
    }

    function getPrevData($user_name)
    {
        return $this->select('user_id')
            ->where('user_name <', $user_name)
            ->orderBy('user_name', 'desc')
            ->limit(1)
            ->first();
    }

    function getNextData($user_name)
    {
        return $this->select('user_id')
            ->where('user_name >', $user_name)
            ->orderBy('user_name', 'asc')
            ->limit(1)
            ->first();
    }
}
