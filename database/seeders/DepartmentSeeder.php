<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departments')->insert([
            ['name' => 'Human Resources', 'head_id' => null],
            ['name' => 'Assemby', 'head_id' => null],
            ['name' => 'Finance', 'head_id' => null],
            ['name' => 'IT', 'head_id' => null],
        ]);
    }
}
