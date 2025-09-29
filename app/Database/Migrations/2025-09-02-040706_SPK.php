<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

use function PHPSTORM_META\map;

class SPK extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
            ],
            'kategori' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
            ],
            'code' => [
                'type' => "VARCHAR",
                'constraint' => '50',
                'null' => false,
            ],
            'lokasi' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
            'dept' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
            ],
            'pelapor' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
            ],
            'tgl_lapor' => [
                'type' => "DATE",
                'null' => false
            ],
            'material' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
            ],
            'material_name' => [
                'type' => "VARCHAR",
                'constraint' => 150,
                'null' => true,
            ],
            'material_model' => [
                'type' => "VARCHAR",
                'constraint' => 250,
                'null' => true,
            ],
            'nomor_mesin' => [
                'type' => "VARCHAR",
                'constraint' => 150,
                'null' => true,
            ],
            'leader' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
            ],
            'defect' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
            ],
            'sub_defect' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
            ],
            'berulang' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
            ],
            'posisi' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
            ],
            'alasan_repair' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
            ],
            'deskripsi' => [
                'type' => "TEXT",
                'null' => false
            ],
            'dokumen_status' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
                'comment' => '0 -> Created, 1 -> Submit, 2 -> Approve, 3 -> On progress in Mold, 4 -> On progress in Planner, 5 -> On progress in Quality, 6 -> Hold, 7 -> Reject, 8 -> close'
            ],
            'status_level' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
                'default' => '0',
                'comment' => '0 : normal, 1 : urgent, 2 : very urgent, 3 : critical'
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

        $this->forge->addKey(['id', 'code'], true, true);
        $this->forge->addUniqueKey('id', 'id');
        $this->forge->addUniqueKey('code', 'code');
        $this->forge->addUniqueKey('tgl_lapor', 'tgl_lapor');
        $this->forge->addUniqueKey('material', 'material');

        $this->forge->createTable('t_spk');
    }

    public function down()
    {
        $this->forge->dropTable('t_spk');
    }
}
