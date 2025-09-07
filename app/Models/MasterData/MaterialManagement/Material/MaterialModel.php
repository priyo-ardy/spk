<?php

namespace App\Models\MasterData\MaterialManagement\Material;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table            = 'm_material';
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

    public function generatePartList()
    {
        return $this->where('kategori', 'b3cf99d9-30f5-4e3b-8c3c-6dcecaff260a')
            ->orderBy('code', 'asc')
            ->findAll();
    }

    public function getPartData($id)
    {
        return $this->where('kategori', 'b3cf99d9-30f5-4e3b-8c3c-6dcecaff260a')
            ->where('id', $id)
            ->first();
    }
}
