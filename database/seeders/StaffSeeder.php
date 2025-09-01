<?php

namespace Database\Seeders;

use App\Enums\Staff\StaffDepartmentEnums;
use App\Enums\RoleEnums;
use App\Enums\Staff\StaffPositionEnums;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user_id = User::create([
            'role' => RoleEnums::SuperAdministrator->value,
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('123456'),
            ]);

        Staff::create([
                    'user_id' => $user_id->id,
                    'last_name' => 'Isaiah',
                    'first_name' => 'Johnson',
                    'other_name' => 'Olamide',
                    'gender' => 'male',
                    'phone_number' => '09030398366',
                    'position'   =>  StaffPositionEnums::ITAdministrator->value,
                    'department' =>  StaffDepartmentEnums::IT->value,
                    'date_of_appointment'  => now()->toDateString()
        ]);


        $user_id = User::create([
            'role' => RoleEnums::Administrator->value,
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            ]);

        Staff::create([
                    'user_id' => $user_id->id,
                    'last_name' => 'Afen',
                    'first_name' => 'Blessing',
                    'other_name' => null,
                    'gender' => 'female',
                    'phone_number' => '07016682945',
        ]);

        $user_id = User::create([
            'role' => RoleEnums::Staff->value,
            'email' => 'staff@gmail.com',
            'password' => bcrypt('123456'),
            ]);

        Staff::create([
                    'user_id' => $user_id->id,
                    'last_name' => 'Isaiah',
                    'first_name' => 'Johnson',
                    'other_name' => 'Olamide',
                    'gender' => 'male',
                    'phone_number' => '08024004029',
        ]);
    }
}
