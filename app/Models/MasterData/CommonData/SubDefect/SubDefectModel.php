<?php

namespace App\Models\MasterData\CommonData\SubDefect;

use CodeIgniter\Model;

class SubDefectModel extends Model
{
    protected $table            = 'm_sub_defect';
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

    function getListByDefect($defect){
        return $this->where('defect', $defect)
                    ->orderBy('name', 'asc')
                    ->findAll();
    }
}
