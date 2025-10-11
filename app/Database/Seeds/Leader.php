<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Leader extends Seeder
{
    public function run()
    {
        $data = [
            [
                "id" => "232984fa-109f-4057-a7cb-f53b4321d843",
                "NIK" => "0009",
                "nama" => "JOKO PRASETYO UTOMO",
                "remark" => "",
                "created_at" => "2025-08-21 10:39:36",
                "created_by" => "0092",
                "updated_at" => "2025-08-21 10:39:36",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "9cc4c173-1543-42a0-9a86-8c80be65f8bc",
                "NIK" => "0013",
                "nama" => "AGUS SETIANTO",
                "remark" => "",
                "created_at" => "2025-08-21 10:39:36",
                "created_by" => "0092",
                "updated_at" => "2025-08-21 10:39:36",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "b12783dc-5d69-43b5-b474-b2038f552a53",
                "NIK" => "0014",
                "nama" => "EDI SALURIYANTO",
                "remark" => "",
                "created_at" => "2025-08-21 10:40:50",
                "created_by" => "0092",
                "updated_at" => "2025-08-21 10:40:50",
                "updated_by" => null,
                "deleted_at" => null
            ]
        ];

        $this->db->table('m_leader')->insertBatch($data);
    }
}
