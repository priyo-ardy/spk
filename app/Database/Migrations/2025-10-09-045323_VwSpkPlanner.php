<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VwSpkPlanner extends MigratiON
{
    public function  up()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_t_spk_planer");
        $this->db->query("
            CREATE VIEW vw_t_spk_planer AS
            SELECT 
                ts.id,
                ts.code ,
                tsm.plan_selesai,
                tsm.aktual_selesai,
                tsm.keterangan AS analisa_awal,
                ts.lokasi,
                ml.name AS nama_lokasi,
                ts.dept,
                md.name AS nama_dept,
                ts.pelapor,
                mk.NIK,
                mk.nama,
                ts.tgl_lapor,
                ts.material,
                mm.code AS kode_material,
                mm.name AS nama_material,
                mm.model AS model_material,
                ts.nomor_mesin,
                ts.leader,
                ml1.NIK AS nik_leader,
                ml1.nama AS nama_leader,
                ts.defect,
                md1.name AS nama_defect,
                ts.sub_defect,
                msd.name AS nama_sub_defect,
                ts.tipe_equipment,
                CASE
                    WHEN ts.tipe_equipment = '1' THEN 'Machine equipment'
                    WHEN ts.tipe_equipment = '2' THEN 'TransportatiON  equipment'
                    WHEN ts.tipe_equipment = '3' THEN 'Final inspectiON  equipment'
                    WHEN ts.tipe_equipment = '4' THEN 'Laboratorium equipment'
                    WHEN ts.tipe_equipment = '5' THEN 'ElectrON ic equipment'
                    WHEN ts.tipe_equipment = '6' THEN 'Other Equipment'
                    else '-'
                END AS nama_tipe_equipment,
                ts.alasan_repair,
                mr.name AS nama_alasan_repair,
                ts.deskripsi,
                ts.deleted_at
            FROM t_spk ts 
                LEFT JOIN t_spk_mold tsm ON  ts.id = tsm.id_spk
                LEFT JOIN m_lokasi ml ON  ts.lokasi = ml.id 
                LEFT JOIN m_dept md ON  ts.dept = md.id
                LEFT JOIN m_karyawan mk ON  ts.pelapor = mk.id
                LEFT JOIN m_material mm ON  ts.material = mm.id
                LEFT JOIN m_leader ml1 ON  ts.leader = ml1.id
                LEFT JOIN m_defect md1 ON  ts.defect = md1.id
                LEFT JOIN m_sub_defect msd ON  ts.sub_defect = msd.id 
                LEFT JOIN m_repair mr ON  ts.alasan_repair = mr.id
            WHERE 
                ts.kategori in('1', '2') AND
                ts.dokumen_status = '2' AND
                ts.flow_status  = '1' AND
                ts.tipe_equipment IN ('', '1') AND
                tsm.status_dokumen = '1'
        ");
    }

    public function  down()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_t_spk_planer");
    }
}
