<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Dept extends Seeder
{
    public function run()
    {
        $data = [
            [
                "id" => "3cd77321-baf8-4c74-be22-b691bd0107db",
                "code" => "DPT-0001",
                "name" => "Manufacturing",
                "remark" => null,
                "created_at" => "2025-08-26 16:35:40",
                "created_by" => "0092",
                "updated_at" => "2025-08-26 16:34:52",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "4cc6f0ea-e4c0-44ac-a648-050fd03f8e26",
                "code" => "DPT-0002",
                "name" => "Quality",
                "remark" => null,
                "created_at" => "2025-08-26 16:35:40",
                "created_by" => "0092",
                "updated_at" => "2025-08-26 16:34:52",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "7f5a75d2-a817-4db7-9b7e-1b8485ee3061",
                "code" => "DPT-0003",
                "name" => "RnD",
                "remark" => null,
                "created_at" => "2025-08-26 16:35:40",
                "created_by" => "0092",
                "updated_at" => "2025-08-26 16:34:52",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "2ba78b2a-dd38-4609-b33b-1e8345513b2e",
                "code" => "DPT-0004",
                "name" => "Warehouse & Logistic",
                "remark" => null,
                "created_at" => "2025-08-26 16:35:40",
                "created_by" => "0092",
                "updated_at" => "2025-08-26 16:34:52",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "aaf8b760-6072-4530-b37d-a5d3ecbaac49",
                "code" => "DPT-0005",
                "name" => "Office",
                "remark" => null,
                "created_at" => "2025-08-26 16:35:40",
                "created_by" => "0092",
                "updated_at" => "2025-08-26 16:34:52",
                "updated_by" => null,
                "deleted_at" => null
            ]
        ];

        $this->db->table('m_dept')->insertBatch($data);
    }
}
