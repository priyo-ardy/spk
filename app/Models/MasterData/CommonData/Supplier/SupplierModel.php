<?php

namespace App\Models\MasterData\CommonData\Supplier;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table            = 'm_supplier';
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

    public function getPrevData($code)
    {
        $this->where('code <', $code)
            ->orderBy('code', 'desc')
            ->limit(1);

        return $this->first();
    }

    public function getNextData($code)
    {
        $this->where('code >', $code)
            ->orderBy('code', 'asc')
            ->limit(1);

        return $this->first();
    }

    public function generateList()
    {
        return $this->orderBy('name', 'asc')->findAll();
    }
}
