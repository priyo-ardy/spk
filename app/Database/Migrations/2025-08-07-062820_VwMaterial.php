<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VwMaterial extends Migration
{
    public function up()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_material");
        $this->db->query("
            CREATE VIEW vw_material AS
            select a.id, b.name as kategori, a.code, a.name, a.cust_part_name, c.name AS satuan, d.name as workshop, a.deleted_at from m_material as a left join m_material_category as b on a.kategori = b.id left join m_satuan as c on a.uom = c.id left join m_workshop as d on a.workshop = d.id;
        ");
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_material");
    }
}
