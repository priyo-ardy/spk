<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MaterialCategory extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false,
            ],
            'code' => [
                'type' => "VARCHAR",
                'constraint' => 20,
                'null' => false,
            ],
            'name' => [
                'type' => "VARCHAR",
                'constraint' => 150,
                'null' => false,
            ],
            'prefix' => [
                'type' => "VARCHAR",
                'constraint' => 45,
                'null' => true
            ],
            'remark' => [
                'type' => "text",
                'null' => true
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
            ],
        ]);

        $this->forge->addKey(['id', 'code'], true, true);
        $this->forge->addUniqueKey('id', 'id');
        $this->forge->addUniqueKey('code', 'code');

        $this->forge->createTable('m_material_category');
    }

    public function down()
    {
        $this->forge->dropTable('m_material_category');
    }
}
