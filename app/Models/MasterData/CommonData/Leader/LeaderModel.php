<?php

namespace App\Models\MasterData\CommonData\Leader;

use CodeIgniter\Model;

class LeaderModel extends Model
{
    protected $table            = 'm_leader';
    protected $primaryKey       = 'id';
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

    public function generateList(){
        return $this->orderBy('NIK', 'ASC')->findAll();
    }
}
