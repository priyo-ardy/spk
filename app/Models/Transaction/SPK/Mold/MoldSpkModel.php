<?php

namespace App\Models\Transaction\SPK\Mold;

use CodeIgniter\Model;

class MoldSpkModel extends Model
{
    protected $table            = 't_spk_mold';
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

    public function getOpenTransaction()
    {
        return $this->from('vw_t_spk_mold')
            ->where('tgl_lapor <=', now())
            ->where('status_dokumen', '0')
            ->orWhere('status_dokumen', null)
            ->get()
            ->getResult();
    }

    public function updatePlannerConfirm($id_spk, $data)
    {
        return $this->db->table('t_spk_mold')
            ->where('id_spk', $id_spk)
            ->update($data);
    }

    public function updateMoldFinish($id_spk, $data)
    {
        return $this->db->table('t_spk_mold')
            ->where('id_spk', $id_spk)
            ->update($data);
    }
}
