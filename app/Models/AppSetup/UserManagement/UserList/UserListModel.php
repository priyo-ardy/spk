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

    public function checkUser($user_name)
    {
        return $this->where('user_name', $user_name)->first();
    }

    public function checkUserPhone(string $phone)
    {
        return $this->where('phone_hash', $phone)->first();
    }

    public function checkUserEmail($email_address)
    {
        return $this->where('email_hash', $email_address)->first();
    }
}
