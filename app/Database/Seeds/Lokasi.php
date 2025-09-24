<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Lokasi extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => generate_uuid(),
                'code' => 'LOC-0001',
                'name' => 'Main Factory',
                'remark' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'created_by' => '0092',
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
                'deleted_at' => null
            ],
            [
                'id' => generate_uuid(),
                'code' => 'LOC-0002',
                'name' => 'Warehose',
                'remark' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'created_by' => '0092',
                'updated_at' => date("Y-m-d H:i:s"),
                'updated_at' => null,
                'deleted_at' => null
            ]
        ];

        $this->db->table('m_lokasi')->insertBatch($data);
    }
}
