<?php

namespace App\Models\MasterData\CommonData\Tonnage;

use CodeIgniter\Model;

class TonnageModel extends Model
{
    protected $table            = 'm_tonnage';
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

    public function getDataById($id)
    {
        return $this->where('id', $id)->first();
    }

    function generateList()
    {
        return $this->orderBy('name', 'asc')->findAll();
    }
}
