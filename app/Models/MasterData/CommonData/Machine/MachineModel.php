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

    public function getDataById($id)
    {
        return $this->where('id', $id)->first();
    }

    public function prevData($code)
    {
        return $this
            ->select('id, code')
            ->where('code <', $code)
            ->orderBy('code', 'desc')
            ->limit(1)
            ->first();
    }

    public function nextData($code)
    {
        return $this
            ->select('id, code')
            ->where('code >', $code)
            ->orderBy('code', 'asc')
            ->limit(1)
            ->first();
    }

    function generateMachineList()
    {
        return $this->orderBy('code', 'asc')->findAll();
    }

    function getMachineData($id_mesin)
    {
        return $this->where('id', $id_mesin)->first();
    }
}
