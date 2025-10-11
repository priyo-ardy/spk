<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Users extends Seeder
{
    public function run()
    {
        $data = [
            [
                'user_id'       => 'ab5cdb92-0de5-41fd-8700-b4df6217cbe3',
                'user_name'     => 'admin',
                'full_name'     => 'Admin',
                'user_password' => '$2y$10$VEbWsKANABYnTEurVl3z0eG0lzfOXgmWTUJsUqN0YHP6riZe.2Uem',
                'user_email'    => enkripsi('admin@example.com'),
                'user_phone'    => enkripsi('08123456789'),
                'user_level'    => '0',
                'user_status'   => '1',
                'last_login'    => null,
                'login_from'    => null,
                'user_image'    => 'user.png',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => null,
            ],
            [
                'user_id'            => '7089fb83-21d2-494d-91ee-d76a1098b432',
                'user_name'      => 'user',
                'full_name'     => 'User',
                'user_password' => '$2y$10$Y3bEco4p9/qKlgRU5Ub73.m9Ur.hgg9FDXD0OHddUdi9gKIpWkS.W',
                'user_email'     => enkripsi('user@example.com'),
                'user_phone'     => enkripsi('08987654321'),
                'user_level'     => '1',
                'user_status'    => '1',
                'last_login'    => null,
                'login_from'    => null,
                'user_image'    => 'user.png',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => null,
            ],
            [
                'user_id'            => 'cfe32aeb-2a05-47b5-89d9-f5712d37ba7e',
                'user_name'      => '0092',
                'full_name'     => 'Ardy Priyo Sudiyantoko',
                'user_password' => '$2y$10$2ggHq3cX9sMd0UgcY.Cem.hLWjaiaEPRp21dwgMRC.ZE94y8de6h6',
                'user_email'     => enkripsi('priyo.ardy@schlemmer.co.id'),
                'user_phone'     => enkripsi('087878475545'),
                'user_level'     => '1',
                'user_status'    => '1',
                'last_login'    => null,
                'login_from'    => null,
                'user_image'    => 'user.png',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => null,
            ],
        ];

        $this->db->table('user_auth')->insertBatch($data);
    }
}
