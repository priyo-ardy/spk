<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VwTEquipmentSPK extends Migration
{
    public function up()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_t_equipment_spk");
        $this->db->query("
            CREATE VIEW vw_t_equipment_spk AS

            SELECT 
                A.*,
                B.name AS nama_dept,
                C.nama AS nama_pelapor,
                D.name AS equipment_name,
                E.name AS tipe_equipment,
                F.nama AS nama_leader,
                CASE
                    WHEN A.status = '0' THEN 'Created'
                END AS nama_status
            FROM t_spk_equipment AS A
                LEFT JOIN m_dept AS B ON A.dept = B.id
                LEFT JOIN m_karyawan AS C ON A.report_by = C.id
                LEFT JOIN m_mesin AS D ON A.equipment = D.id
                LEFT JOIN m_tipe_equipment AS E ON A.equipment_type = E.id
                LEFT JOIN m_leader AS F ON A.leader = F.id
        ");
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_t_equipment_spk");
    }
}
