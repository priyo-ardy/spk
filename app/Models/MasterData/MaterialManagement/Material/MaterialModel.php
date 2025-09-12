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
        $data = [];
        $query = $this->where('kategori', 'b3cf99d9-30f5-4e3b-8c3c-6dcecaff260a')
            ->orderBy('code', 'asc')
            ->findAll();
        foreach ($query as $item) {
            $data[] = [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name,
                'model' => $item->model,
                'no_mesin' => ''
            ];
        }

        return $data;
    }

    public function getPartData($id)
    {
        $data = [];
        $query = $this->where('kategori', 'b3cf99d9-30f5-4e3b-8c3c-6dcecaff260a')
            ->where('id', $id)
            ->first();

        $data = [
            'name' => $query->name,
            'model' => $query->model,
            'code' => $query->code,
            'no_mesin' => ''
        ];

        return $data;
    }
}
