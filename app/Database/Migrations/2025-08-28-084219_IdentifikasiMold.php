<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class IdentifikasiMold extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => "VARCHAR",
                'constraint' => 50,
                'null' => false
            ],
        ]);
    }

    public function down()
    {
        //
    }
}
