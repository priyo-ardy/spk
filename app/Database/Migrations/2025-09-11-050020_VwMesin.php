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
            select a.id, b.name as workshop, a.code, a.nomor_mesin, a.name, a.spesifikasi, a.tonnage, a.brand, a.serial_no, a.rate, a.mfg_date, a.purchase_date, a.remark, a.deleted_at from m_mesin a join m_workshop b on a.workshop = b.id;
        ");
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_mesin");
    }
}
