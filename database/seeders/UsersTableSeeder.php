<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = new \App\Models\Admin();
        $admin->name = 'Admin';
        $admin->email = 'admin@mlm.com';
        $admin->password = bcrypt('password');
        $admin->save();

        $employee = new \App\Models\Employee();
        $employee->name = 'Employee';
        $employee->email = 'employee@mlm.com';
        $employee->phone = '1234567890';
        $employee->password = bcrypt('password');
        $employee->save();

    }
}
