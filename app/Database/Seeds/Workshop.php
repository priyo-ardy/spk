<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Workshop extends Seeder
{
    public function run()
    {
        $data = [
            [
                "id" => "bb4f442d-79d3-41d6-8339-012348e69b02",
                "code" => "WRS-0001",
                "name" => "Injection",
                "remark" => "",
                "created_at" => "2025-08-20 02:03:47",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:03:47",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "913e3df2-ab90-452b-9ce3-3eb97d65da4f",
                "code" => "WRS-0002",
                "name" => "Assembly",
                "remark" => "",
                "created_at" => "2025-08-20 02:03:53",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:03:53",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "673eee28-ce92-4f3b-b496-48ffcfc40c16",
                "code" => "WRS-0003",
                "name" => "Laser Printing",
                "remark" => "",
                "created_at" => "2025-08-20 02:03:59",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:03:59",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "11ffcc23-e07a-48a5-acf0-f40eaed572ad",
                "code" => "WRS-0004",
                "name" => "Final Inspection",
                "remark" => "",
                "created_at" => "2025-08-20 02:04:06",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:04:06",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "493c9c6c-d058-4e0f-a6a9-b5f5314107c3",
                "code" => "WRS-0005",
                "name" => "Raw Material",
                "remark" => "",
                "created_at" => "2025-08-20 02:04:17",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:04:17",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "d5f953a8-6d42-4ed7-9784-e836db963de2",
                "code" => "WRS-0006",
                "name" => "Auxiliary",
                "remark" => "",
                "created_at" => "2025-08-20 02:04:25",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:04:25",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "33f3b171-2898-4e83-9c73-51740a9b35d2",
                "code" => "WRS-0007",
                "name" => "Office",
                "remark" => "",
                "created_at" => "2025-08-20 02:04:29",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:04:29",
                "updated_by" => null,
                "deleted_at" => null
            ]
        ];

        $this->db->table('m_workshop')->insertBatch($data);
    }
}
