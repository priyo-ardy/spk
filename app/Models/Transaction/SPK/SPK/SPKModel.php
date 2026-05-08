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
        $this->db->transStart();

        try {
            $sql = "SELECT RIGHT(code, 8) as kode_angka 
                FROM " . $this->table . " 
                ORDER BY CAST(RIGHT(code, 8) AS UNSIGNED) DESC 
                LIMIT 1 
                FOR UPDATE";

            $query = $this->db->query($sql)->getRow();

            $next_number = ($query) ? (int)$query->kode_angka + 1 : 1;
            $generated_code = "$prefix-$tanggal-$mold_no-" . str_pad($next_number, 8, '0', STR_PAD_LEFT);

            $this->db->transComplete();

            // return $generated_code;
            if ($this->db->transStatus() === false) {
                return null; // Gagal karena masalah database
            }

            return $generated_code;
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
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

    public function cekDokumenStatus($id_spk)
    {
        return $this->whereIn('id', $id_spk)->get()->getResultObject();
    }

    function updateIdentifikasi($id_spk)
    {
        return $this->whereIn('id', $id_spk)
            ->set(['identifikasi' => '1'])
            ->update();
    }
}
