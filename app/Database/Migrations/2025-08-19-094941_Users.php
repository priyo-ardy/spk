<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'user_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'user_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'user_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'user_password' => [
                'type' => 'TEXT',
                'null' => false,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'user_status' => [
                'type' => 'VARCHAR',
                'constraint' => 1,
                'default' => '1',
                'comment' => '0 -> Disabled, 1 -> Enabled',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'user_level' => [
                'type' => 'VARCHAR',
                'constraint' => '1',
                'null' => false,
                'comment' => '0 -> Super Administrator, 1 -> Administrator, 2 -> Admin Manager, 3 -> Admin Leader, 4 -> User',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'user_image' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'default' => 'user.png',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'last_login' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ],
            'login_from' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'default' => null
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ],
            'updated_by' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'default' => null
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ]
        ]);

        $this->forge->addKey(['user_id', 'user_name', 'user_phone', 'user_email'], true, true);
        $this->forge->createTable('user_auth');
    }

    public function down()
    {
        $this->forge->dropTable('user_auth');
    }
}
