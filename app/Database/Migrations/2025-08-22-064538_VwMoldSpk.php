<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VwMoldSpk extends Migration
{
    public function up()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_mold_spk");
        $this->db->query("
            CREATE VIEW vw_mold_spk AS
            select 
                a.*,
                b.name as nama_dept,
                c.nama  as nama_karyawan,
                d.code as kode_part,
                e.name as nama_alasan_repair,
                f.name as nama_defect,
                g.name as nama_sub_defect,
                h.name as nama_posisi_defect,
                case
                    when a.berulang = '0' then 'No'
                    else 'Yes'
                end as nama_berulang
            from t_spk_mold as a
                left join m_dept as b on a.dept = b.id 
                left join m_karyawan as c on a.report_by = c.id
                left join m_material as d on a.part_no = d.id
                left join m_repair as e on a.repair_reason = e.id
                left join m_defect as f on a.defect = f.id
                left join m_sub_defect as g on a.sub_defect = g.id
                left join m_posisi_defect as h on a.position = h.id
            order by a.code desc;
        ");
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_mold_spk");
    }
}
