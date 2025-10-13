<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VwDefect extends Migration
{
    public function up()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_defect");
        $this->db->query(
            "
                CREATE VIEW vw_defect AS
                SELECT 
                    m_defect.id,
                    m_defect.kategori,
                    CASE
                        WHEN m_defect.kategori = '1' THEN 'SPK Mold Repair'
                        WHEN m_defect.kategori = '2' THEN 'SPK Mesin'
                        WHEN m_defect.kategori = '3' THEN 'SPK Preventive Maintenance Request'
                        WHEN m_defect.kategori = '4' THEN 'SPK Equipment Request'
                    END AS nama_kategori,
                    m_defect.code,
                    m_defect.name,
                    m_defect.description,
                    m_defect.status,
                    m_defect.created_at,
                    m_defect.created_by,
                    m_defect.updated_at,
                    m_defect.updated_by,
                    m_defect.deleted_at
                FROM m_defect
                ORDER BY m_defect.code ASC
            "
        );
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_defect");
    }
}
