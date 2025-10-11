<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EquipmentType extends Seeder
{
    public function run()
    {
        $data = [
            [
                "id" => "45976d47-3970-4485-a66d-ade1be86f7c4",
                "code" => "ECT-0001",
                "name" => "Machine Equipment",
                "remark" => null,
                "created_at" => "2025-08-21 10:35:53",
                "created_by" => "0092",
                "updated_at" => "2025-08-21 10:35:53",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "b4e17a40-67b4-4e23-856e-cf9316ff1986",
                "code" => "ECT-0002",
                "name" => "Transportation Equipment",
                "remark" => null,
                "created_at" => "2025-08-21 10:35:53",
                "created_by" => "0092",
                "updated_at" => "2025-08-21 10:35:53",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "36fe54b7-9224-482a-b2f8-654dd0f3d197",
                "code" => "ECT-0003",
                "name" => "Final Inspection",
                "remark" => null,
                "created_at" => "2025-08-21 10:35:53",
                "created_by" => "0092",
                "updated_at" => "2025-08-21 10:35:53",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "d20b9f81-0679-4779-8daa-4de9580fec11",
                "code" => "ECT-0004",
                "name" => "Laboratorium Equipment",
                "remark" => null,
                "created_at" => "2025-08-21 10:35:53",
                "created_by" => "0092",
                "updated_at" => "2025-08-21 10:35:53",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "4c9dd36d-a22f-4076-aeef-03bb8be69fa3",
                "code" => "ECT-0005",
                "name" => "Electronic Equipment",
                "remark" => null,
                "created_at" => "2025-08-21 10:35:53",
                "created_by" => "0092",
                "updated_at" => "2025-08-21 10:35:53",
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "14afbd07-e04f-4d06-91cd-16c5b1e73a47",
                "code" => "ECT-0006",
                "name" => "Other Equipment",
                "remark" => null,
                "created_at" => "2025-08-21 10:35:53",
                "created_by" => "0092",
                "updated_at" => "2025-08-21 10:35:53",
                "updated_by" => null,
                "deleted_at" => null
            ]
        ];
        $this->db->table('m_tipe_equipment')->insertBatch($data);
    }
}
