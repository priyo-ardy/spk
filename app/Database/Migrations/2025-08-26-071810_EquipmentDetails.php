<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EquipmentDetails extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
            'id_spk' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
            'urut' => [
                'type' => "INT",
                'constraint' => 11,
                'null' => false,
                'default' => 1
            ],
            'file_name' => [
                'type' => "VARCHAR",
                'constraint' => 255,
                'null' => false
            ],
            'file_size' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
            'file_path' => [
                'type' => "VARCHAR",
                'constraint' => 255,
                'null' => false
            ],
            'created_at' => [
                'type' => "DATETIME",
                'null' => true,
                'default' => null
            ],
            'created_by' => [
                'type' => "VARCHAR",
                'constraint' => 20,
                'null' => true
            ],
            'updated_at' => [
                'type' => "DATETIME",
                'null' => true,
                'default' => null
            ],
            'updated_by' => [
                'type' => "VARCHAR",
                'constraint' => 20,
                'null' => true
            ],
            'deleted_at' => [
                'type' => "DATETIME",
                'null' => true,
                'default' => null
            ]
        ]);

        $this->forge->addKey('id', true, true);
        $this->forge->createTable('t_spk_equipment_details');
    }

    public function down()
    {
        $this->forge->dropTable('t_spk_equipment_details');
    }
}
