<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VwSubDefect extends Migration
{
    public function up()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_sub_defect");
        $this->db->query("
            CREATE VIEW vw_sub_defect AS
            SELECT 
                a.id, 
                a.defect,
                b.name as nama_defect,
                a.name,
                a.keterangan,
                a.status,
                a.created_at,
                a.created_by,
                a.updated_at,
                a.updated_by,
                a.deleted_at 
            FROM m_sub_defect as a 
                left JOIN m_defect AS b ON a.defect = b.id 
            order BY a.code ASC
        ");
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_sub_defect");
    }
}
