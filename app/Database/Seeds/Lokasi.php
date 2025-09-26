<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Lokasi extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => '434040cd-2994-44ef-b38b-e46d5c27e51c',
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
                'id' => 'b31eff01-3a49-465a-9bc6-2f8d3b5cd358',
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
