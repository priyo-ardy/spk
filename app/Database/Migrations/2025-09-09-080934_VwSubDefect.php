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
            select a.id, a.code, a.name, b.name as defect, a.keterangan, a.deleted_at from m_sub_defect as a join m_defect as b on a.defect = b.id;
        ");
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_sub_defect");
    }
}
