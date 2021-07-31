<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'             => 1,
                'first_name'           => 'Admin',
                'last_name'           => 'Admin',
                'email'          => 'admin@admin.com',
                'password'       => '$2y$10$mpESFRG3sVMKh0xoKda8NOzGw4AYi3Ld9aoFVGTlrYAIgeLHzC4li',
                'password_plain' => 'password',
                'remember_token' => null,
                'mobile_number'  => '9999999999'
            ],
        ];

        User::insert($users);
    }
}
