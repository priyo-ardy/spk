<?php

namespace App\Models\MasterData\CommonData\Lokasi;

use CodeIgniter\Model;

class LokasiModel extends Model
{
    protected $table            = 'm_lokasi';
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

    function generateList()
    {
        return $this->orderBy('name', 'asc')->findAll();
    }

    public function getDataById($id)
    {
        return $this->where('id', $id)->first();
    }
}
