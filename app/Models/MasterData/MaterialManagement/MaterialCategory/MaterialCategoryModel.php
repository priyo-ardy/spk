<?php

namespace App\Models\MasterData\MaterialManagement\MaterialCategory;

use CodeIgniter\Model;

class MaterialCategoryModel extends Model
{
    protected $table            = 'm_material_category';
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

    public function getDataById($category_id)
    {
        return $this->where('id', $category_id, true)->first();
    }

    function generateList()
    {
        return $this->orderBy('name', 'asc')->findAll();
    }

    function checkPrefix($id)
    {
        return $this->where('id', $id, true)->first();
    }
}
