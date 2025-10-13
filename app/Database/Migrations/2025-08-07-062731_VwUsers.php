<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VwUsers extends Migration
{
    public function up()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_user_auth");
        $this->db->query("
            CREATE VIEW vw_user_auth AS
            SELECT 
                *,
                CASE
                    WHEN user_level = '0' THEN 'Super Administrator'
                    WHEN user_level = '1' THEN 'Administrator'
                    WHEN user_level = '2' THEN 'Planner'
                    WHEN user_level = '3' THEN 'Engineer'
                    WHEN user_level = '4' THEN 'Quality'
                    WHEN user_level = '5' THEN 'User'
                END as nama_level,
                CASE
                    WHEN user_status = '1' THEN 'Enable'
                    WHEN user_status = '0' THEN 'Disable'
                END AS nama_status
            FROM user_auth
        ");
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS vw_user_auth");
    }
}
