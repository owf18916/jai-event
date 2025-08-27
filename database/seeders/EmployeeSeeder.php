<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::create([
            'employee_code' => '018916',
            'name' => 'OWF'
        ]);

        Employee::create([
            'employee_code' => '018917',
            'name' => 'RKO'
        ]); 
    }
}
