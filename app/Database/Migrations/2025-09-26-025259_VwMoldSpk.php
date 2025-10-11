<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VwMoldSpk extends Migration
{
    public function up()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_t_spk_mold");
        $this->db->query("
            CREATE VIEW vw_t_spk_mold AS
            SELECT 
                a.id,
                a.code,
                a.tgl_lapor,
                a.lokasi,
                c.name AS nama_lokasi,
                a.dept,
                d.name AS nama_dept,
                a.pelapor,
                e.NIK,
                e.nama AS nama_pelapor,
                f.code AS kode_material,
                f.name AS nama_material,
                f.model AS model_material,
                a.nomor_mesin,
                a.alasan_repair,
                g.name AS nama_alasan_repair,
                a.deskripsi,
                b.status_dokumen,
                CASE
                    WHEN b.status_dokumen = '0' THEN 'Open'
                    WHEN b.status_dokumen = '1' THEN 'Confirm'
                    WHEN b.status_dokumen = '2' THEN 'On Progress'
                    WHEN b.status_dokumen = '3' THEN 'Hold'
                    WHEN b.status_dokumen = '4' THEN 'Cancel'
                    WHEN b.status_dokumen = '5' THEN 'Close'
                    else 'Open'
                END AS status,
                b.plan_selesai,
                b.aktual_selesai,
                a.deleted_at
            from t_spk AS a
                LEFT JOIN t_spk_mold AS b ON a.id = b.id_spk
                LEFT JOIN m_lokasi AS c ON a.lokasi = c.id
                LEFT JOIN m_dept AS d ON a.dept = d.id
                LEFT JOIN m_karyawan AS e ON a.pelapor = e.id
                LEFT JOIN m_material AS f ON a.material = f.id
                LEFT JOIN m_repair AS g ON a.alasan_repair = g.id
            WHERE 
                a.kategori = '1'
            ORDER BY a.tgl_lapor ASC
        ");
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_t_spk_mold");
    }
}
