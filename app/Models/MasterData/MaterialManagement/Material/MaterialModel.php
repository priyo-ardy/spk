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
        return $this->where('kategori', 'b3cf99d9-30f5-4e3b-8c3c-6dcecaff260a')->orderBy('code', 'asc')->findAll();
    }

    function getMaterialData($id_material)
    {
        return $this->where('id', $id_material)->get()->getRow();
    }

    public function generateList()
    {
        return $this->orderBy('code', 'asc')->findAll();
    }

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

    function generateCodeMaterial($table, $column, $prefix, $length)
    {
        $builder = $this->db->table($table);

        $query = $builder->select('RIGHT(' . $column . ', ' . $length . ') AS kode')
            ->like($column, $prefix, 'BOTH')
            ->orderBy($column, 'DESC')
            ->limit(1)
            ->get();

        $result = $query->getRow();

        if ($result) {
            $kode = (int) $result->kode + 1;
        } else {
            $kode = 1;
        }

        $generated_code = $prefix . str_pad($kode, $length, '0', STR_PAD_LEFT);

        return $generated_code;
    }

    function getMaterialByCategory($category)
    {
        switch ($category) {
            case 1:
                return $this->orderBy('code', 'asc')->findAll();
                break;
            case 2:
                return $this->db->table('m_mesin')->where('deleted_at', null)->orderBy('code', 'ASC')->get()->getResultObject();
                break;
        }
    }
}
