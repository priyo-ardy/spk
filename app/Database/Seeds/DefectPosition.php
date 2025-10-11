<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DefectPosition extends Seeder
{
    public function run()
    {
        $data = [
            [
                "id" => "B2C64B61-882A-4F13-9D05-4D9A3169C25A",
                "code" => "DFP-00001",
                "name" => "Lock Male",
                "keterangan" => "",
                "status" => "1",
                "created_at" => null,
                "created_by" => null,
                "updated_at" => null,
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "F44E4533-0F2F-4C07-BE14-2D623CE1F556",
                "code" => "DFP-00002",
                "name" => "Lock Female",
                "keterangan" => "",
                "status" => "1",
                "created_at" => null,
                "created_by" => null,
                "updated_at" => null,
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "57B81058-8498-4F45-AAD8-D873FFCC28EA",
                "code" => "DFP-00003",
                "name" => "Clip",
                "keterangan" => "",
                "status" => "1",
                "created_at" => null,
                "created_by" => null,
                "updated_at" => null,
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "1680CD79-C111-4C70-978D-8BE1002CBDCA",
                "code" => "DFP-00004",
                "name" => "Hinge",
                "keterangan" => "",
                "status" => "1",
                "created_at" => null,
                "created_by" => null,
                "updated_at" => null,
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "7794DE24-2ECA-4842-80E3-113EEB77D867",
                "code" => "DFP-00005",
                "name" => "Ejector",
                "keterangan" => "",
                "status" => "1",
                "created_at" => null,
                "created_by" => null,
                "updated_at" => null,
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "A9FD3739-FC01-4A17-8F93-89F45D8B1AE6",
                "code" => "DFP-00006",
                "name" => "COT Stopper",
                "keterangan" => "",
                "status" => "1",
                "created_at" => null,
                "created_by" => null,
                "updated_at" => null,
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "C8CC607E-210E-4045-8EED-5461E1E5A648",
                "code" => "DFP-00007",
                "name" => "Lubang Cable Ties",
                "keterangan" => "",
                "status" => "1",
                "created_at" => null,
                "created_by" => null,
                "updated_at" => null,
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "A44C428D-C5C3-40BB-A5D8-50F25CF99B33",
                "code" => "DFP-00008",
                "name" => "Collar",
                "keterangan" => "",
                "status" => "1",
                "created_at" => null,
                "created_by" => null,
                "updated_at" => null,
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "3AA17525-2D6A-455A-B528-ECBD24CE1039",
                "code" => "DFP-00009",
                "name" => "Assembly item",
                "keterangan" => "",
                "status" => "1",
                "created_at" => null,
                "created_by" => null,
                "updated_at" => null,
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "DF86F752-DFC3-483A-A503-83C5A2021FEB",
                "code" => "DFP-00010",
                "name" => "Laser printing",
                "keterangan" => "",
                "status" => "1",
                "created_at" => null,
                "created_by" => null,
                "updated_at" => null,
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "FF9AD70A-3AD2-4A26-9FE7-8B9DB2FA8A37",
                "code" => "DFP-00011",
                "name" => "Gate",
                "keterangan" => "",
                "status" => "1",
                "created_at" => null,
                "created_by" => null,
                "updated_at" => null,
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "185AED50-8689-4D5B-8D44-545F21833458",
                "code" => "DFP-00012",
                "name" => "Bracket area",
                "keterangan" => "",
                "status" => "1",
                "created_at" => null,
                "created_by" => null,
                "updated_at" => null,
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "0F12523B-30EE-4B1F-9D1D-6F51CB2EBD88",
                "code" => "DFP-00013",
                "name" => "Area General",
                "keterangan" => "",
                "status" => "1",
                "created_at" => null,
                "created_by" => null,
                "updated_at" => null,
                "updated_by" => null,
                "deleted_at" => null
            ],
            [
                "id" => "C6B91A1E-9D3B-4EAE-A880-CB28EA986194",
                "code" => "DFP-00014",
                "name" => "Final Filling Position",
                "keterangan" => "",
                "status" => "1",
                "created_at" => null,
                "created_by" => null,
                "updated_at" => null,
                "updated_by" => null,
                "deleted_at" => null
            ]
        ];

        $this->db->table('m_posisi_defect')->insertBatch($data);
    }
}
