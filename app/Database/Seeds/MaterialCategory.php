<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MaterialCategory extends Seeder
{
    public function run()
    {
        $data = [
            [
                "id" => "d40102ed-f432-4ce6-af65-6814b4cbc974",
                "code" => "MTC-0001",
                "name" => "Part",
                "remark" => "",
                "prefix" => "",
                "created_at" => "2025-08-20 01:48:10",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 01:48:10",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "4d704e24-16ff-4987-8beb-80fb1858c6a2",
                "code" => "MTC-0002",
                "name" => "Raw Material",
                "remark" => "",
                "prefix" => "",
                "created_at" => "2025-08-20 01:48:16",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 01:48:16",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "ed76d967-516f-4fbf-8245-23c0854b9ca0",
                "code" => "MTC-0003",
                "name" => "Child Parts",
                "remark" => "",
                "prefix" => "",
                "created_at" => "2025-08-20 01:48:22",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 01:48:22",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "a74b5500-d89d-42e5-80c8-dae17e778d24",
                "code" => "MTC-0004",
                "name" => "Auxiliary",
                "remark" => "",
                "prefix" => "",
                "created_at" => "2025-08-20 01:48:30",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 01:48:30",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "a20d7cb6-484a-4763-9e82-969a83775728",
                "code" => "MTC-0005",
                "name" => "Spareparts",
                "remark" => "",
                "prefix" => "SPR-",
                "created_at" => "2025-08-20 01:48:35",
                "created_by" => "0092",
                "updated_at" => "2025-08-20 01:48:35",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "93000380-bb84-4e8e-95cc-0eb33b406798",
                "code" => "MTC-0006",
                "name" => "Machine",
                "remark" => null,
                "prefix" => "MCH-",
                "created_at" => "2025-09-08 09:28:36",
                "created_by" => "0092",
                "updated_at" => "2025-09-08 09:28:36",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "5c0d8857-4018-4e08-b44e-49ef315d5e87",
                "code" => "MTC-0007",
                "name" => "Machine Equipment",
                "remark" => null,
                "prefix" => "MEQ-",
                "created_at" => "2025-09-08 09:28:36",
                "created_by" => "0092",
                "updated_at" => "2025-09-08 09:28:36",
                "updated_by" => null,
                "deleted_at" => null
            ]
        ];

        $this->db->table('m_material_category')->insertBatch($data);
    }
}
