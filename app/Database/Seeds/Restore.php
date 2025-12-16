<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Restore extends Seeder
{
    public function run()
    {
        $this->call('Users');
        $this->call('Workshop');
        $this->call('Tonnage');
        $this->call('EquipmentType');
        $this->call('MaterialCategory');
        $this->call('MaterialList');
        $this->call('Defect');
        $this->call('SubDefect');
        $this->call('DefectPosition');
        $this->call('Dept');
        $this->call('Karyawan');
        $this->call('Leader');
        $this->call('Lokasi');
        $this->call('Machine');
        $this->call('RepairReason');
        $this->call('Satuan');
        $this->call('ProblemCategoryData');
    }
}
