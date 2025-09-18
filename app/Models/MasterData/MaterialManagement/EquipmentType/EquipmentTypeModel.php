<?php

namespace App\Models\MasterData\MaterialManagement\EquipmentType;

use CodeIgniter\Model;

class EquipmentTypeModel extends Model
{
    protected $table            = 'm_tipe_equipment';
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

    public function getDataById($type_id)
    {
        return $this->where('id', $type_id, true)->first();
    }
}
