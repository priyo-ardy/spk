<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VwMesin extends Migration
{
    public function up()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_mesin");
        $this->db->query("
            CREATE VIEW vw_mesin AS
            SELECT 
                a.*,
                b.name as nama_workshop
            FROM m_mesin a 
                LEFT JOIN m_workshop b ON a.workshop = b.id
            ORDER BY a.code ASC
        ");
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_mesin");
    }
}
