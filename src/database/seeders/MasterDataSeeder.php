<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\ClassroomStudent;
use App\Models\Grade;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\TeacherSubject;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Academic Year ─────────────────────────────────────
        $ay = AcademicYear::firstOrCreate(
            ['name' => '2025/2026'],
            [
                'start_date' => '2025-07-14',
                'end_date' => '2026-06-20',
                'is_active' => true,
            ]
        );

        // ── Semesters ─────────────────────────────────────────
        $semester1 = Semester::firstOrCreate(
            ['academic_year_id' => $ay->id, 'semester_number' => 1],
            [
                'name' => 'Semester 1 (Ganjil)',
                'start_date' => '2025-07-14',
                'end_date' => '2025-12-20',
                'is_active' => false,
            ]
        );

        $semester2 = Semester::firstOrCreate(
            ['academic_year_id' => $ay->id, 'semester_number' => 2],
            [
                'name' => 'Semester 2 (Genap)',
                'start_date' => '2026-01-05',
                'end_date' => '2026-06-20',
                'is_active' => true,
            ]
        );

        // ── Grades ────────────────────────────────────────────
        $grades = [];
        $gradeData = [
            ['name' => 'Kelas VII', 'level' => 7, 'description' => 'Kelas 7 SMP'],
            ['name' => 'Kelas VIII', 'level' => 8, 'description' => 'Kelas 8 SMP'],
            ['name' => 'Kelas IX', 'level' => 9, 'description' => 'Kelas 9 SMP'],
        ];

        foreach ($gradeData as $g) {
            $grades[$g['level']] = Grade::firstOrCreate(
                ['level' => $g['level']],
                ['name' => $g['name'], 'description' => $g['description']]
            );
        }

        // ── Subjects ──────────────────────────────────────────
        $subjectsData = [
            ['code' => 'MTK', 'name' => 'Matematika', 'description' => 'Mata pelajaran Matematika'],
            ['code' => 'BIN', 'name' => 'Bahasa Indonesia', 'description' => 'Mata pelajaran Bahasa Indonesia'],
            ['code' => 'BIG', 'name' => 'Bahasa Inggris', 'description' => 'Mata pelajaran Bahasa Inggris'],
            ['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam', 'description' => 'Mata pelajaran IPA'],
            ['code' => 'IPS', 'name' => 'Ilmu Pengetahuan Sosial', 'description' => 'Mata pelajaran IPS'],
            ['code' => 'PKN', 'name' => 'Pendidikan Kewarganegaraan', 'description' => 'Mata pelajaran PKn'],
            ['code' => 'PAI', 'name' => 'Pendidikan Agama Islam', 'description' => 'Mata pelajaran PAI'],
            ['code' => 'PJK', 'name' => 'Pendidikan Jasmani', 'description' => 'Mata pelajaran PJOK'],
            ['code' => 'SNB', 'name' => 'Seni Budaya', 'description' => 'Mata pelajaran Seni Budaya'],
            ['code' => 'TIK', 'name' => 'Informatika', 'description' => 'Mata pelajaran Informatika'],
        ];

        $subjects = [];
        foreach ($subjectsData as $s) {
            $subjects[$s['code']] = Subject::firstOrCreate(
                ['code' => $s['code']],
                ['name' => $s['name'], 'description' => $s['description']]
            );
        }

        // ── Get existing teacher from UserSeeder ──────────────
        $teacher = \App\Models\Teacher::whereHas('user', fn ($q) => $q->where('email', 'teacher@maleo.test'))->first();

        // ── Classrooms ────────────────────────────────────────
        $classrooms = [];
        $classroomData = [
            ['grade' => 7, 'name' => 'VII-A', 'capacity' => 32],
            ['grade' => 7, 'name' => 'VII-B', 'capacity' => 32],
            ['grade' => 8, 'name' => 'VIII-A', 'capacity' => 30],
            ['grade' => 8, 'name' => 'VIII-B', 'capacity' => 30],
            ['grade' => 9, 'name' => 'IX-A', 'capacity' => 28],
            ['grade' => 9, 'name' => 'IX-B', 'capacity' => 28],
        ];

        foreach ($classroomData as $c) {
            $classrooms[$c['name']] = Classroom::firstOrCreate(
                ['name' => $c['name']],
                [
                    'grade_id' => $grades[$c['grade']]->id,
                    'homeroom_teacher_id' => $c['name'] === 'VII-A' && $teacher ? $teacher->id : null,
                    'capacity' => $c['capacity'],
                ]
            );
        }

        // ── Teacher-Subject assignments ───────────────────────
        if ($teacher) {
            $teacherSubjectCodes = ['MTK', 'IPA', 'TIK'];
            foreach ($teacherSubjectCodes as $code) {
                TeacherSubject::firstOrCreate([
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subjects[$code]->id,
                ]);
            }
        }

        // ── Assign student to classroom ───────────────────────
        $student = \App\Models\Student::whereHas('user', fn ($q) => $q->where('email', 'student@maleo.test'))->first();
        if ($student) {
            ClassroomStudent::firstOrCreate([
                'classroom_id' => $classrooms['VII-A']->id,
                'student_id' => $student->id,
                'academic_year_id' => $ay->id,
            ]);
        }

        // ── Schedules for VII-A (Semester 2) ──────────────────
        if ($teacher) {
            $scheduleData = [
                // Monday
                ['day' => 'monday', 'subject' => 'PAI', 'start' => '07:00', 'end' => '07:40', 'room' => 'R-101'],
                ['day' => 'monday', 'subject' => 'PAI', 'start' => '07:40', 'end' => '08:20', 'room' => 'R-101'],
                ['day' => 'monday', 'subject' => 'MTK', 'start' => '08:20', 'end' => '09:00', 'room' => 'R-101', 'teacher' => true],
                ['day' => 'monday', 'subject' => 'MTK', 'start' => '09:15', 'end' => '09:55', 'room' => 'R-101', 'teacher' => true],
                ['day' => 'monday', 'subject' => 'BIN', 'start' => '09:55', 'end' => '10:35', 'room' => 'R-101'],
                ['day' => 'monday', 'subject' => 'BIN', 'start' => '10:35', 'end' => '11:15', 'room' => 'R-101'],

                // Tuesday
                ['day' => 'tuesday', 'subject' => 'IPA', 'start' => '07:00', 'end' => '07:40', 'room' => 'Lab IPA', 'teacher' => true],
                ['day' => 'tuesday', 'subject' => 'IPA', 'start' => '07:40', 'end' => '08:20', 'room' => 'Lab IPA', 'teacher' => true],
                ['day' => 'tuesday', 'subject' => 'BIG', 'start' => '08:20', 'end' => '09:00', 'room' => 'R-101'],
                ['day' => 'tuesday', 'subject' => 'BIG', 'start' => '09:15', 'end' => '09:55', 'room' => 'R-101'],
                ['day' => 'tuesday', 'subject' => 'PKN', 'start' => '09:55', 'end' => '10:35', 'room' => 'R-101'],
                ['day' => 'tuesday', 'subject' => 'PKN', 'start' => '10:35', 'end' => '11:15', 'room' => 'R-101'],

                // Wednesday
                ['day' => 'wednesday', 'subject' => 'IPS', 'start' => '07:00', 'end' => '07:40', 'room' => 'R-101'],
                ['day' => 'wednesday', 'subject' => 'IPS', 'start' => '07:40', 'end' => '08:20', 'room' => 'R-101'],
                ['day' => 'wednesday', 'subject' => 'MTK', 'start' => '08:20', 'end' => '09:00', 'room' => 'R-101', 'teacher' => true],
                ['day' => 'wednesday', 'subject' => 'SNB', 'start' => '09:15', 'end' => '09:55', 'room' => 'R-Seni'],
                ['day' => 'wednesday', 'subject' => 'SNB', 'start' => '09:55', 'end' => '10:35', 'room' => 'R-Seni'],
                ['day' => 'wednesday', 'subject' => 'PJK', 'start' => '10:35', 'end' => '11:15', 'room' => 'Lapangan'],

                // Thursday
                ['day' => 'thursday', 'subject' => 'BIN', 'start' => '07:00', 'end' => '07:40', 'room' => 'R-101'],
                ['day' => 'thursday', 'subject' => 'BIG', 'start' => '07:40', 'end' => '08:20', 'room' => 'R-101'],
                ['day' => 'thursday', 'subject' => 'TIK', 'start' => '08:20', 'end' => '09:00', 'room' => 'Lab Komputer', 'teacher' => true],
                ['day' => 'thursday', 'subject' => 'TIK', 'start' => '09:15', 'end' => '09:55', 'room' => 'Lab Komputer', 'teacher' => true],
                ['day' => 'thursday', 'subject' => 'IPA', 'start' => '09:55', 'end' => '10:35', 'room' => 'R-101', 'teacher' => true],
                ['day' => 'thursday', 'subject' => 'PJK', 'start' => '10:35', 'end' => '11:15', 'room' => 'Lapangan'],

                // Friday
                ['day' => 'friday', 'subject' => 'PAI', 'start' => '07:00', 'end' => '07:40', 'room' => 'R-101'],
                ['day' => 'friday', 'subject' => 'IPS', 'start' => '07:40', 'end' => '08:20', 'room' => 'R-101'],
                ['day' => 'friday', 'subject' => 'PKN', 'start' => '08:20', 'end' => '09:00', 'room' => 'R-101'],
                ['day' => 'friday', 'subject' => 'SNB', 'start' => '09:15', 'end' => '09:55', 'room' => 'R-Seni'],
            ];

            foreach ($scheduleData as $s) {
                // Use the seeded teacher for subjects they teach, otherwise use the teacher as placeholder
                $teacherId = $teacher->id;

                Schedule::firstOrCreate([
                    'classroom_id' => $classrooms['VII-A']->id,
                    'subject_id' => $subjects[$s['subject']]->id,
                    'semester_id' => $semester2->id,
                    'day' => $s['day'],
                    'start_time' => $s['start'],
                ], [
                    'teacher_id' => $teacherId,
                    'end_time' => $s['end'],
                    'room' => $s['room'],
                ]);
            }
        }
    }
}
