<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'module' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'action_status' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'pages' => [
                'type'       => 'text',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'message' => [
                'type'       => 'TEXT',
                'null'       => true,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'user_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'hostname' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'user_agent' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'old_data' => [
                'type'       => 'TEXT',
                'null'       => true,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'new_data' => [
                'type'       => 'TEXT',
                'null'       => true,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default' => null
            ],
            'status' => [
                'type' => 'vARCHAR',
                'constraint' => 1,
                'null' => false,
                'default' => '1',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'deleted_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default' => null
            ]
        ]);

        $this->forge->addKey(['id'], true);

        $this->forge->createTable('user_log', true);
    }

    public function down()
    {
        $this->forge->dropTable('user_log', true, true);
    }
}
