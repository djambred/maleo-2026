<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin (SIAKAD)
        $user = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('password')]
        );
        $user->assignRole('super_admin');

        // Teacher (Hub)
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@maleo.test'],
            ['name' => 'Jefry Sunupurwa Asri', 'password' => Hash::make('password')]
        );
        $teacher->assignRole('teacher');
        \App\Models\Teacher::firstOrCreate(
            ['user_id' => $teacher->id],
            ['nip' => '198501012010011001', 'gender' => 'male', 'status' => 'active']
        );

        // Student (Hub)
        $student = User::firstOrCreate(
            ['email' => 'student@maleo.test'],
            ['name' => 'Aisyah Putri', 'password' => Hash::make('password')]
        );
        $student->assignRole('student');
        \App\Models\Student::firstOrCreate(
            ['user_id' => $student->id],
            ['nis' => '20250001', 'nisn' => '0012345678', 'gender' => 'female', 'status' => 'active']
        );

        // Parent (Connect)
        $parent = User::firstOrCreate(
            ['email' => 'parent@maleo.test'],
            ['name' => 'Ahmad Hidayat', 'password' => Hash::make('password')]
        );
        $parent->assignRole('parent');
        $guardian = \App\Models\Guardian::firstOrCreate(
            ['user_id' => $parent->id],
            ['gender' => 'male', 'phone' => '081234567890', 'occupation' => 'Entrepreneur']
        );
        // Link parent to student
        $studentModel = \App\Models\Student::where('user_id', $student->id)->first();
        if ($studentModel && !$guardian->students()->where('student_id', $studentModel->id)->exists()) {
            $guardian->students()->attach($studentModel->id, ['relationship' => 'father']);
        }
    }
}
