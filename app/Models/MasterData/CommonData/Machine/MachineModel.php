<?php

namespace App\Models\MasterData\CommonData\Machine;

use CodeIgniter\Model;

class MachineModel extends Model
{
    protected $table            = 'm_mesin';
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
        return $this->orderBy('code', 'asc')->findAll();
    }

    function generateMachineList()
    {
        $data = [];

        $query = $this->db->escape($this->orderBy('code', 'asc')->findAll());

        foreach ($query as $item) {
            $data[] = [
                'id' => $item->id,
                'code' => $item->code,
                'name' => $item->name . ' - ' . $item->nomor_mesin,
                'model' => $item->model,
                'no_mesin' => $item->nomor_mesin
            ];
        }

        return $data;
    }

    function getMachineData($id)
    {
        $data = [];
        $query = $this->where('id', $id, true)->first();

        $data = [
            'name' => $query->name,
            'model' => $query->model,
            'code' => $query->code,
            'no_mesin' => $query->nomor_mesin
        ];

        return $data;
    }
}
