<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MoldSPK extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
            'code' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
            'dept' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
            'report_by' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
            'report_date' => [
                'type' => 'DATE',
                'null' => false
            ],
            'part_no' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
            'part_name' => [
                'type' => "VARCHAR",
                'constraint' => 150,
                'null' => false
            ],
            'part_model' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
            'mold_no' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
            'repair_reason' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
            'description' => [
                'type' => "TEXT",
                'null' => false
            ],
            'status' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
                'default' => '0'
            ],
            'created_at' => [
                'type' => "DATETIME",
                'null' => true,
                'default' => null
            ],
            'created_by' => [
                'type' => "VARCHAR",
                'constraint' => 20,
                'null' => true,
                'default' => null
            ],
            'updated_at' => [
                'type' => "DATETIME",
                'null' => true,
                'default' => null
            ],
            'updated_by' => [
                'type' => "VARCHAR",
                'constraint' => 20,
                'null' => true,
                'default' => null
            ],
            'deleted_at' => [
                'type' => "DATETIME",
                'null' => true,
                'default' => null
            ]
        ]);

        $this->forge->addKey(['id', 'code'], true, true);
        $this->forge->addUniqueKey('id', 'id');
        $this->forge->addUniqueKey('code', 'code');

        $this->forge->createTable('t_spk_mold');
    }

    public function down()
    {
        $this->forge->dropTable('t_spk_mold');
    }
}
