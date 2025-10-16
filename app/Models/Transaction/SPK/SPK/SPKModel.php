<?php

namespace App\Models\Transaction\SPK\SPK;

use CodeIgniter\Model;

class SPKModel extends Model
{
    protected $table            = 't_spk';
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

    function generateDocNo(string $prefix, string $tanggal, string $mold_no)
    {
        $query = $this->select("RIGHT(code, 8) as kode", true)
            ->orderBy('code', 'desc')
            ->limit(1)
            ->first();

        if ($query) {
            $kode = (int) $query->kode + 1;
        } else {
            $kode = 1;
        }

        $generated_code = "$prefix-$tanggal-$mold_no-" . str_pad($kode, 8, '0', STR_PAD_LEFT);

        return $generated_code;
    }

    function getPrevData($code)
    {
        return $this
            ->select('id, code')
            ->where('code <', $code)
            ->orderBy('code', 'desc')
            ->limit(1)
            ->first();
    }

    public function getNextData($code)
    {
        return $this
            ->select('id, code')
            ->where('code >', $code)
            ->orderBy('code', 'asc')
            ->limit(1)
            ->first();
    }

    public function getPlannerSpk($id)
    {
        return $this->db
            ->table('vw_t_spk_planer')
            ->where('id', $id)
            ->get()
            ->getRow();
    }
}
