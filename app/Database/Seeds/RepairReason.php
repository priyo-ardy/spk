<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RepairReason extends Seeder
{
    public function run()
    {
        $data = [
            [
                "id" => "9f63d744-4efa-47cc-96c7-0d49a90c8804",
                "code" => "RPR-0001",
                "name" => "Internal quality problem",
                "remark" => null,
                "created_at" => "2025-08-21 15:01:39",
                "created_by" => "0092",
                "updated_at" => "2025-08-21 15:01:39",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "118148c2-d14b-48b4-a97d-2df61a41b9a2",
                "code" => "RPR-0002",
                "name" => "External quality problem",
                "remark" => null,
                "created_at" => "2025-08-21 15:01:39",
                "created_by" => "0092",
                "updated_at" => "2025-08-21 15:01:39",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "a2fc6254-4dad-434b-bb93-0f6024c13b6f",
                "code" => "RPR-0003",
                "name" => "Engineering change - Initiated by customer",
                "remark" => null,
                "created_at" => "2025-08-21 15:01:39",
                "created_by" => "0092",
                "updated_at" => "2025-08-21 15:01:39",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "b074a6ea-5af1-4c5b-8ef0-f193107f45e0",
                "code" => "RPR-0004",
                "name" => "Engineering change - Initiated schlemmer",
                "remark" => null,
                "created_at" => "2025-08-21 15:01:39",
                "created_by" => "0092",
                "updated_at" => "2025-08-21 15:01:39",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "387f2ea0-f091-44bd-b758-0f3a0fed765d",
                "code" => "RPR-0005",
                "name" => "Other",
                "remark" => null,
                "created_at" => "2025-08-21 15:01:39",
                "created_by" => "0092",
                "updated_at" => "2025-08-21 15:01:39",
                "updated_by" => null,
                "deleted_at" => null
            ]
        ];

        $this->db->table('m_repair')->insertBatch($data);
    }
}
