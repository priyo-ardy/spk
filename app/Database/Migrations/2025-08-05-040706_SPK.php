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
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'kategori' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'code' => [
                'type' => "VARCHAR",
                'constraint' => '50',
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'lokasi' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'dept' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'pelapor' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'tgl_lapor' => [
                'type' => "DATE",
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'mold_confirm' => [
                'type' => "DATETIME",
                'null' => true,
                'default' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'planner_confirm' => [
                'type' => "DATETIME",
                'null' => true,
                'default' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'mold_finish' => [
                'type' => "DATETIME",
                'null' => true,
                'default' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'quality_confirm' => [
                'type' => "DATE",
                'null' => true,
                'default' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'material' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'material_name' => [
                'type' => "VARCHAR",
                'constraint' => 150,
                'null' => true,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'material_model' => [
                'type' => "VARCHAR",
                'constraint' => 250,
                'null' => true,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'nomor_mesin' => [
                'type' => "VARCHAR",
                'constraint' => 150,
                'null' => true,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'leader' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'defect' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'sub_defect' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'berulang' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'prioritas' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
                'comment' => '0. Low, 1. Normal, 2. Urgent, 3. Critical',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'tipe_equipment' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => true,
                'default' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'alasan_repair' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'lokasi_repair' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
                'default' => '1',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'supplier' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => true,
                'default' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'jig_status' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
                'default' => '1',
                'comment' => '0. Before SOP, 1. After SOP',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'deskripsi' => [
                'type' => "TEXT",
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'dokumen_status' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
                'comment' => '0 -> Created, 1 -> Submit, 2 -> Approve, 3 -> On progress in Mold, 4 -> On progress in Planner, 5 -> On progress in Quality, 6 -> Hold, 7 -> Reject, 8 -> close',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'identifikasi' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
                'default' => '0',
                'comment' => '0 -> Belum Ada Identifikasi, 1 -> Sudah Ada Identifikasi',
            ],
            'verifikasi' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
                'default' => '0',
                'comment' => '0 -> Belum Ada Verifikasi, 1 -> Sudah Ada Verifikasi',
            ],
            'level_status' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
                'default' => '0',
                'comment' => '0 : normal, 1 : urgent, 2 : very urgent, 3 : critical',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'flow_status' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => false,
                'default' => '0',
                'comment' => '1 : mold enginner confirm, 2 : planner confirm, 3 : ME confirm, 4 : mold engineer finish, 5 : ME finish, 6 : quality confirm',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'appearance' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => true,
                'default' => null,
                'comment' => '0 : NG, 1 : OK, 2 : Not Checked',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'dimension' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => true,
                'default' => null,
                'comment' => '0 : NG, 1 : OK, 2 : Not Checked',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'performance' => [
                'type' => "VARCHAR",
                'constraint' => 1,
                'null' => true,
                'default' => null,
                'comment' => '0 : NG, 1 : OK, 2 : Not Checked',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'created_at' => [
                'type' => "DATETIME",
                'null' => true,
                'default' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'created_by' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => true,
                'default' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'updated_at' => [
                'type' => "DATETIME",
                'null' => true,
                'default' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'updated_by' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => true,
                'default' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'deleted_at' => [
                'type' => "DATETIME",
                'null' => true,
                'default' => null,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]
        ]);

        $this->forge->addKey(['id', 'code'], true, true);
        $this->forge->addUniqueKey('id', 'id');
        $this->forge->addUniqueKey('code', 'code');

        $this->forge->createTable('t_spk', true);
    }

    public function down()
    {
        $this->forge->dropTable('t_spk', true);
    }
}
