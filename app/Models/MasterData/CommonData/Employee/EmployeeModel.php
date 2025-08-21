<?php

namespace App\Models\MasterData\CommonData\Employee;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table            = 'm_karyawan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = false;

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
