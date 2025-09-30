<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Mesin extends Migration
{
    /**
     * Membuat tabel mesin
     *
     * @return void
     */
     public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
            'workshop' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
            'code' => [
                'type' => "VARCHAR",
                'constraint' => 20,
                'null' => false
            ],
            'nomor_mesin' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => true
            ],
            'name' => [
                'type' => "VARCHAR",
                'constraint' => 150,
                'null' => false
            ],
            'spesification' => [
                'type' => "TEXT",
                'null' => true
            ],
            'tonnage' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => true
            ],
            'brand' => [
                'type' => "VARCHAR",
                'constraint' => 150,
                'null' => true
            ],
            'serial_no' => [
                'type' => "VARCHAR",
                'constraint' => 150,
                'null' => true
            ],
            'rate' => [
                'type' => "VARCHAR",
                'constraint' => 150,
                'null' => true
            ],
            'mfg_date' => [
                'type' => "DATE",
                'null' => true
            ],
            'purchase_date' => [
                'type' => "DATE",
                'null' => true
            ],
            'remark' => [
                'type' => "TEXT",
                'null' => true
            ],
            'created_at' => [
                'type' => "DATETIME",
                'null' => true
            ],
            'created_by' => [
                'type' => "VARCHAR",
                'constraint' => 20,
                'null' => true
            ],
            'updated_at' => [
                'type' => "DATETIME",
                'null' => true
            ],
            'updated_by' => [
                'type' => "VARCHAR",
                'constraint' => 20,
                'null' => true
            ],
            'deleted_at' => [
                'type' => "DATETIME",
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true, true);
        $this->forge->addUniqueKey('id', 'id');
        $this->forge->addUniqueKey('code', 'code');

        $this->forge->createTable('m_mesin');
    }

/**
 * Drop m_mesin table
 */
    public function down()
    {
        $this->forge->dropTable('m_mesin');
    }
}
