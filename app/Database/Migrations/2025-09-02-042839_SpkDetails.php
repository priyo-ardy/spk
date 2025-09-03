<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SpkDetails extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
            ],
            'urut' => [
                'type' => "INT",
                'constraint' => 11,
                'default' => 1,
                'null' => false
            ],
            'id_spk' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
            ],
            'nama_file' => [
                'type' => "VARCHAR",
                'constraint' => 250,
                'null' => false,
            ],
            'ukuran_file' => [
                'type' => "DECIMAL",
                'constraint' => "11,3",
                'null' => false,
                'default' => 0
            ],
            'created_at' => [
                'type' => "DATETIME",
                'null' => true,
                'default' => null
            ],
            'created_by' => [
                'type' => "VARCHAR",
                'constraint' => 50,
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
                'constraint' => 50,
                'null' => true,
                'default' => null
            ],
            'deleted_at' => [
                'type' => "DATETIME",
                'null' => true,
                'default' => null
            ]
        ]);

        $this->forge->addKey('id', true, true);
        $this->forge->addUniqueKey('id_spk', 'id_spk');

        $this->forge->createTable('t_spk_details');
    }

    public function down()
    {
        $this->forge->dropTable('t_spk_details');
    }
}
