<?php

namespace App\Models\Transaction\Identification;

use CodeIgniter\Model;

class IdentificationModel extends Model
{
    protected $table            = 't_identification';
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

    public function checkSPK($spk)
    {
        $query = "
            SELECT t_spk.id FROM t_spk WHERE t_spk.id IN ($spk) and t_spk.id NOT IN (SELECT t_identification.id_spk FROM t_identification)
        ";

        return $this->db->query($query)->getResultObject();
    }

    public function getIdentificationById($id_identification)
    {
        return $this->where('t_identification.id', $id_identification)
            ->select('
                t_identification.*, 
                t_spk.code as kode_spk,
                t_spk.tgl_lapor as tgl_lapor,
                t_spk.kategori,
                t_spk.defect,
                t_spk.dept,
                t_spk.pelapor,
                t_spk.material,
                t_spk.material_model,
                t_spk.alasan_repair,
                t_spk.sub_defect,
                t_spk.berulang,
                t_spk.lokasi_repair,
                t_spk.supplier,
                t_spk.leader
            ')
            ->join('t_spk', 't_identification.id_spk = t_spk.id', 'left')
            ->first();
    }

    public function getIdentificationBySpk($id_spk)
    {
        return $this->where('id_spk', $id_spk)
            ->select('t_identification.*, t_spk.code')
            ->join('t_spk', 't_identification.id_spk = t_spk.id', 'left')
            ->first();
    }
}
