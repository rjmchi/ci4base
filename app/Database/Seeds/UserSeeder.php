<?php

namespace App\Database\Seeds;

use App\Models\User;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $mdl = new User();

        $mdl->insert(
            ['name'=>'jane',
            'email'=>'jane@jetson.com',
            'password'=>password_hash('george', PASSWORD_BCRYPT)],
        );
    }
}
