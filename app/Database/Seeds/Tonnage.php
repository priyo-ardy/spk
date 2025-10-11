<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Tonnage extends Seeder
{
    public function run()
    {
        $data = [
            [
                "id" => "cf2a5bf9-cff3-4388-9044-f3093e237700",
                "code" => "TNG-0001",
                "name" => "160T",
                "remark" => "",
                "created_at" => "2025-08-20 02:37:02",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:37:02",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "024c9ae6-9c33-4fc7-ab43-a0a9a955a8b5",
                "code" => "TNG-0002",
                "name" => "250T",
                "remark" => "",
                "created_at" => "2025-08-20 02:37:12",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:37:12",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "9ec3dc9c-e226-40bd-b04a-5eb3603b4f24",
                "code" => "TNG-0003",
                "name" => "280T",
                "remark" => "",
                "created_at" => "2025-08-20 02:37:17",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:37:17",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "2826a77e-9d34-446f-a3af-6e65c11443bd",
                "code" => "TNG-0004",
                "name" => "380T",
                "remark" => "",
                "created_at" => "2025-08-20 02:37:22",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:37:22",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "c6087ca8-ce10-43c5-bf5c-e0630f5ad6bb",
                "code" => "TNG-0005",
                "name" => "470T",
                "remark" => "",
                "created_at" => "2025-08-20 02:37:27",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:37:27",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "30f56f93-a50a-43af-b616-a9dadfa1256a",
                "code" => "TNG-0006",
                "name" => "530T",
                "remark" => "",
                "created_at" => "2025-08-20 02:37:33",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:37:33",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "a8df30df-8d5d-4b54-81b6-845729d3dafb",
                "code" => "TNG-0007",
                "name" => "600T",
                "remark" => "",
                "created_at" => "2025-08-20 02:37:38",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:37:38",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "81fd8a1d-0d26-4d5c-a7fa-fa5963b1f3ac",
                "code" => "TNG-0008",
                "name" => "800T",
                "remark" => "",
                "created_at" => "2025-08-20 02:37:43",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:37:43",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "a1b87ed2-75f6-4020-9840-ce4587dea43f",
                "code" => "TNG-0009",
                "name" => "90T",
                "remark" => "",
                "created_at" => "2025-08-20 02:37:49",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:37:49",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "416ed8d9-2933-410e-89f6-7a6f9d7fd4fd",
                "code" => "TNG-0010",
                "name" => "Sumitomo 180T",
                "remark" => "",
                "created_at" => "2025-08-20 02:37:56",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 02:37:56",
                "updated_by" => null,
                "deleted_at" => null
            ]
        ];

        $this->db->table('m_tonnage')->insertBatch($data);
    }
}
