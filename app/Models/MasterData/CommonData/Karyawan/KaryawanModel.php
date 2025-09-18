<?php

namespace App\Models\MasterData\CommonData\Karyawan;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table            = 'm_karyawan';
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
        return $this->orderBy('NIK', 'asc')->findAll();
    }

    public function getDataById($id)
    {
        return $this->where('id', $id)->first();
    }

    public function getEmployeeName($NIK)
    {
        return $this->where('NIK', $NIK)->first();
    }
}
