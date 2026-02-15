<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\LeaveType;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ------------------------------
        // 1️⃣ Seed Roles (Prevent Duplicates)
        // ------------------------------
        $roles = [
            ['name' => 'Admin'],
            ['name' => 'Department Head'],
            ['name' => 'Employee'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                $role
            );
        }

        // ------------------------------
        // 2️⃣ Seed Leave Types
        // ------------------------------
        $leaveTypes = [
            [
                'name' => 'Annual',
                'description' => 'Annual vacation leave',
                'max_days' => 21,
            ],
            [
                'name' => 'Sick',
                'description' => 'Sick leave for medical reasons',
                'max_days' => 14,
            ],
            [
                'name' => 'Emergency',
                'description' => 'Emergency leave for urgent matters',
                'max_days' => 7,
            ],
            [
                'name' => 'Maternity',
                'description' => 'Maternity leave',
                'max_days' => 84,
            ],
            [
                'name' => 'Paternity',
                'description' => 'Paternity leave',
                'max_days' => 7,
            ],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }

        // ------------------------------
        // 3️⃣ Seed Departments (Prevent Duplicates)
        // ------------------------------
        $departments = [
            ['name' => 'Human Resources', 'head_id' => null],
            ['name' => 'Finance', 'head_id' => null],
            ['name' => 'IT', 'head_id' => null],
            ['name' => 'Assembly', 'head_id' => null],
            ['name' => 'Spare Parts', 'head_id' => null],
            ['name' => 'Mechanical', 'head_id' => null],
            ['name' => 'Electrical', 'head_id' => null],
            ['name' => 'Painting', 'head_id' => null],
            ['name' => 'Quality Control', 'head_id' => null],
            ['name' => 'Logistics', 'head_id' => null],
            ['name' => 'Sales & Marketing', 'head_id' => null],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->updateOrInsert(
                ['name' => $dept['name']],
                $dept
            );
        }

        // ------------------------------
        // 4️⃣ Seed Users (Prevent Duplicates)
        // ------------------------------
        $users = [
            // Admin
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@nish.ug',
                'password' => Hash::make('password123'),
                'role_id' => 1,
                'department_id' => null,
                'phone' => null,
                'date_of_birth' => null,
                'gender' => null,
                'address' => null,
                'emergency_contact' => null,
                'employment_type' => 'full_time',
                'join_date' => '2020-01-01',
                'status' => 'active',
            ],
            // ---- Department Heads ----
            [
                'first_name' => 'HR',
                'last_name' => 'Head',
                'email' => 'hrhead@nish.ug',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'department_id' => 1,
                'phone' => null,
                'date_of_birth' => null,
                'gender' => null,
                'address' => null,
                'emergency_contact' => null,
                'employment_type' => 'full_time',
                'join_date' => '2020-01-01',
                'status' => 'active',
            ],
            [
                'first_name' => 'Finance',
                'last_name' => 'Head',
                'email' => 'financehead@nish.ug',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'department_id' => 2,
                'phone' => null,
                'date_of_birth' => null,
                'gender' => null,
                'address' => null,
                'emergency_contact' => null,
                'employment_type' => 'full_time',
                'join_date' => '2020-01-01',
                'status' => 'active',
            ],
            [
                'first_name' => 'IT',
                'last_name' => 'Head',
                'email' => 'ithead@nish.ug',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'department_id' => 3,
                'phone' => null,
                'date_of_birth' => null,
                'gender' => null,
                'address' => null,
                'emergency_contact' => null,
                'employment_type' => 'full_time',
                'join_date' => '2020-01-01',
                'status' => 'active',
            ],
            [
                'first_name' => 'Assembly',
                'last_name' => 'Head',
                'email' => 'assemblyhead@nish.ug',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'department_id' => 4,
                'phone' => null,
                'date_of_birth' => null,
                'gender' => null,
                'address' => null,
                'emergency_contact' => null,
                'employment_type' => 'full_time',
                'join_date' => '2020-01-01',
                'status' => 'active',
            ],
            [
                'first_name' => 'Spare Parts',
                'last_name' => 'Head',
                'email' => 'sparepartshead@nish.ug',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'department_id' => 5,
                'phone' => null,
                'date_of_birth' => null,
                'gender' => null,
                'address' => null,
                'emergency_contact' => null,
                'employment_type' => 'full_time',
                'join_date' => '2020-01-01',
                'status' => 'active',
            ],
            [
                'first_name' => 'Mechanical',
                'last_name' => 'Head',
                'email' => 'mechanicalhead@nish.ug',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'department_id' => 6,
                'phone' => null,
                'date_of_birth' => null,
                'gender' => null,
                'address' => null,
                'emergency_contact' => null,
                'employment_type' => 'full_time',
                'join_date' => '2020-01-01',
                'status' => 'active',
            ],
            [
                'first_name' => 'Electrical',
                'last_name' => 'Head',
                'email' => 'electricalhead@nish.ug',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'department_id' => 7,
                'phone' => null,
                'date_of_birth' => null,
                'gender' => null,
                'address' => null,
                'emergency_contact' => null,
                'employment_type' => 'full_time',
                'join_date' => '2020-01-01',
                'status' => 'active',
            ],
            [
                'first_name' => 'Painting',
                'last_name' => 'Head',
                'email' => 'paintinghead@nish.ug',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'department_id' => 8,
                'phone' => null,
                'date_of_birth' => null,
                'gender' => null,
                'address' => null,
                'emergency_contact' => null,
                'employment_type' => 'full_time',
                'join_date' => '2020-01-01',
                'status' => 'active',
            ],
            [
                'first_name' => 'Quality Control',
                'last_name' => 'Head',
                'email' => 'qchead@nish.ug',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'department_id' => 9,
                'phone' => null,
                'date_of_birth' => null,
                'gender' => null,
                'address' => null,
                'emergency_contact' => null,
                'employment_type' => 'full_time',
                'join_date' => '2020-01-01',
                'status' => 'active',
            ],
            [
                'first_name' => 'Logistics',
                'last_name' => 'Head',
                'email' => 'logisticshead@nish.ug',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'department_id' => 10,
                'phone' => null,
                'date_of_birth' => null,
                'gender' => null,
                'address' => null,
                'emergency_contact' => null,
                'employment_type' => 'full_time',
                'join_date' => '2020-01-01',
                'status' => 'active',
            ],
            [
                'first_name' => 'Sales',
                'last_name' => 'Head',
                'email' => 'saleshead@nish.ug',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'department_id' => 11,
                'phone' => null,
                'date_of_birth' => null,
                'gender' => null,
                'address' => null,
                'emergency_contact' => null,
                'employment_type' => 'full_time',
                'join_date' => '2020-01-01',
                'status' => 'active',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // ------------------------------
        // 5️⃣ Assign Department Heads to Departments
        // ------------------------------
        $deptHeadEmails = [
            'Human Resources'   => 'hrhead@nish.ug',
            'Finance'           => 'financehead@nish.ug',
            'IT'                => 'ithead@nish.ug',
            'Assembly'          => 'assemblyhead@nish.ug',
            'Spare Parts'       => 'sparepartshead@nish.ug',
            'Mechanical'        => 'mechanicalhead@nish.ug',
            'Electrical'        => 'electricalhead@nish.ug',
            'Painting'          => 'paintinghead@nish.ug',
            'Quality Control'   => 'qchead@nish.ug',
            'Logistics'         => 'logisticshead@nish.ug',
            'Sales & Marketing' => 'saleshead@nish.ug',
        ];

        foreach ($deptHeadEmails as $deptName => $headEmail) {
            $head = User::where('email', $headEmail)->first();
            if ($head) {
                DB::table('departments')->where('name', $deptName)->update(['head_id' => $head->id]);
            }
        }

        $this->command->info('Database seeded successfully!');
    }
}