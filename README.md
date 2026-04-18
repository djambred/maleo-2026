# Maleo - School Management System

A comprehensive school management platform built with **Laravel 12**, **Filament 3.3**, and **Docker**, designed for SMP (Sekolah Menengah Pertama) environments. The system consists of three interconnected panels serving different user roles.

---

## Table of Contents

- [Overview](#overview)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Panels](#panels)
- [Database Schema](#database-schema)
- [Models & Relationships](#models--relationships)
- [Roles & Permissions](#roles--permissions)
- [Installation](#installation)
- [Seeded Accounts](#seeded-accounts)
- [Project Structure](#project-structure)

---

## Overview

| Panel | URL | Purpose | Users |
|---|---|---|---|
| **Maleo SIAKAD** | `https://maleo.test/admin` | Academic administration & monitoring | Super Admin, Admin |
| **Maleo Hub** | `https://maleo.test/hub` | Teaching & learning portal | Teacher, Student |
| **Maleo Connect** | `https://maleo.test/connect` | Parent communication portal | Parent |

---

## Tech Stack

| Component | Technology |
|---|---|
| Framework | Laravel 12 |
| Admin Panel | Filament 3.3 (Multi-panel) |
| PHP | 8.3-fpm |
| Database | MariaDB 10.11 |
| Web Server | Nginx (stable-alpine) with SSL |
| Containerization | Docker Compose |
| RBAC | Spatie Permission + Filament Shield |
| CSS | Tailwind CSS (per-panel themes) |
| Domain | `maleo.test` (local SSL) |

### Filament Plugins

| Plugin | Purpose |
|---|---|
| `bezhansalleh/filament-shield` | Role & permission management |
| `hasnayeen/themes` | Theme switcher |
| `njxqlus/filament-progressbar` | Page loading progress bar |
| `diogogpinto/filament-auth-ui-enhancer` | Enhanced login UI |
| `awcodes/light-switch` | Dark/Light mode toggle |
| `awcodes/overlook` | Dashboard resource overview widgets |
| `joaopaulolndev/filament-edit-profile` | User profile editing |
| `z3d0x/filament-logger` | Activity logging |

---

## Architecture

```
┌──────────────────────────────────────────────────────┐
│                    Nginx (SSL)                       │
│                  maleo.test:443                       │
└──────────────┬───────────────────────────────────────┘
               │
┌──────────────▼───────────────────────────────────────┐
│              PHP 8.3-fpm (Laravel 12)                │
│                                                       │
│  ┌─────────────┐ ┌────────────┐ ┌──────────────────┐ │
│  │ Maleo SIAKAD│ │ Maleo Hub  │ │ Maleo Connect    │ │
│  │   /admin    │ │   /hub     │ │   /connect       │ │
│  │  (Blue)     │ │ (Emerald)  │ │  (Amber)         │ │
│  │             │ │            │ │                   │ │
│  │ Admin panel │ │ Teacher &  │ │ Parent portal    │ │
│  │ Full CRUD   │ │ Student    │ │ Read-only view   │ │
│  └─────────────┘ └────────────┘ └──────────────────┘ │
└──────────────┬───────────────────────────────────────┘
               │
┌──────────────▼───────────────────────────────────────┐
│             MariaDB 10.11 (port 13306)               │
│                  Database: maleo                      │
└──────────────────────────────────────────────────────┘
```

---

## Panels

### Maleo SIAKAD (`/admin`) — Admin Panel

Full academic administration with 13 resources:

| Navigation Group | Resources |
|---|---|
| **Master Data** | StudentResource, TeacherResource, GuardianResource |
| **Academic** | AcademicYearResource, GradeResource, SubjectResource, ScheduleResource, AttendanceResource, ScoreResource |
| **Content Management** | SubjectContentResource, TaskResource (with Submissions RelationManager) |
| **Communication** | AnnouncementResource |
| **Administration** | UserResource |

### Maleo Hub (`/hub`) — Teacher & Student Portal

6 resources with role-based behavior:

| Navigation Group | Resources | Teacher | Student |
|---|---|---|---|
| **Academic** | SubjectContentResource | Full CRUD (own content) | View published content |
| **Academic** | TaskResource | Full CRUD (own tasks) + Grade submissions | View tasks + Submit answers |
| **Academic** | ScoreResource | View | View own scores |
| **Academic** | AttendanceResource | View | View own attendance |
| **Schedule** | ScheduleResource | View | View |
| **Communication** | AnnouncementResource | View | View |

### Maleo Connect (`/connect`) — Parent Portal

5 read-only resources:

| Navigation Group | Resources |
|---|---|
| **My Children** | ChildResource (Student model) |
| **Academic** | ScoreResource, AttendanceResource, TaskResource |
| **Communication** | AnnouncementResource |

---

## Dashboard Widgets

### Maleo SIAKAD Dashboard

| Widget | Type | Description |
|---|---|---|
| `StatsOverview` | Stats (4 cards) | Total students, teachers, guardians, classrooms with active counts |
| `AcademicStats` | Stats (4 cards) | Active academic year, current semester, subjects count, schedule entries |
| `AttendanceChart` | Bar Chart | Weekly attendance breakdown (present vs absent/late) per day |
| `TaskSubmissionChart` | Doughnut Chart | Submission status distribution (submitted, graded, returned, late) |
| `LatestAnnouncements` | Table | 5 most recent active announcements |
| `LatestAccessLogs` | Table | Activity log from filament-logger |
| `OverlookWidget` | Overview | Resource overview cards (from awcodes/overlook) |

### Maleo Hub Dashboard

| Widget | Type | Teacher View | Student View |
|---|---|---|---|
| `HubStatsOverview` | Stats (4 cards) | My content, my tasks, pending grading, schedule slots | Available tasks, submitted, pending, avg score |
| `TodaySchedule` | Table | Today's classes (own schedule) | Today's classes (own classroom) |
| `UpcomingTasks` | Table | Upcoming deadlines (own tasks) | Upcoming deadlines (own classroom) |
| `HubAnnouncements` | Table | Announcements for teachers | Announcements for students |

### Maleo Connect Dashboard

| Widget | Type | Description |
|---|---|---|
| `ConnectStatsOverview` | Stats (4 cards) | Children count, task submissions, average score, today's attendance |
| `ChildrenTasks` | Table | Upcoming tasks assigned to children's classrooms |
| `RecentAttendance` | Table | Last 7 attendance records for all children |
| `ConnectAnnouncements` | Table | Announcements targeted to parents |

---

## Database Schema

### Migration 1: School Management Tables (18 tables)

```
students            ← user_id (FK → users)
teachers            ← user_id (FK → users)
guardians           ← user_id (FK → users)
guardian_student     ← guardian_id, student_id (pivot, with 'relationship')

academic_years      ← name, start_date, end_date, is_active
semesters           ← academic_year_id, semester_number, start_date, end_date, is_active

grades              ← name, level, description
classrooms          ← grade_id, homeroom_teacher_id, name, capacity
classroom_students  ← classroom_id, student_id, academic_year_id

subjects            ← code (unique), name, description
teacher_subjects    ← teacher_id, subject_id (unique pair)

schedules           ← classroom_id, subject_id, teacher_id, semester_id, day, start_time, end_time, room
attendances         ← student_id, classroom_id, date, status, notes
scores              ← student_id, subject_id, semester_id, score_type, score, notes
announcements       ← title, content, audience, published_at, is_active, created_by
```

### Migration 2: Content & Tasks Tables (3 tables)

```
subject_contents    ← subject_id, classroom_id, semester_id, teacher_id, title, body, attachment, order, is_published
tasks               ← subject_id, classroom_id, semester_id, teacher_id, title, description, type, max_score, due_date, is_published
task_submissions    ← task_id, student_id, answer, attachment, score, feedback, status, submitted_at, graded_at
```

### Entity Relationship Overview

```
User 1──1 Student 1──* ClassroomStudent *──1 Classroom *──1 Grade
User 1──1 Teacher 1──* TeacherSubject   *──1 Subject
User 1──1 Guardian *──* Student (via guardian_student)

AcademicYear 1──* Semester
Classroom ──── Schedule ──── Subject
                   └──────── Teacher
                   └──────── Semester

Student ──── Attendance ──── Classroom
Student ──── Score ──── Subject, Semester
Student ──── TaskSubmission ──── Task

Teacher ──── SubjectContent ──── Subject, Classroom, Semester
Teacher ──── Task ──── Subject, Classroom, Semester
```

---

## Models & Relationships

| Model | Key Relationships |
|---|---|
| `User` | hasOne: Student, Teacher, Guardian |
| `Student` | belongsTo: User · belongsToMany: Guardian · hasMany: ClassroomStudent, Attendance, Score, TaskSubmission |
| `Teacher` | belongsTo: User · hasMany: TeacherSubject, Classroom (homeroom), Schedule, SubjectContent, Task |
| `Guardian` | belongsTo: User · belongsToMany: Student |
| `AcademicYear` | hasMany: Semester, ClassroomStudent |
| `Semester` | belongsTo: AcademicYear · hasMany: Score, Schedule |
| `Grade` | hasMany: Classroom |
| `Classroom` | belongsTo: Grade, Teacher (homeroom) · hasMany: ClassroomStudent, Schedule, Attendance |
| `ClassroomStudent` | belongsTo: Classroom, Student, AcademicYear |
| `Subject` | hasMany: TeacherSubject, Schedule, Score, SubjectContent, Task |
| `TeacherSubject` | belongsTo: Teacher, Subject |
| `Schedule` | belongsTo: Classroom, Subject, Teacher, Semester |
| `Attendance` | belongsTo: Student, Classroom |
| `Score` | belongsTo: Student, Subject, Semester |
| `Announcement` | belongsTo: User (created_by) |
| `SubjectContent` | belongsTo: Subject, Classroom, Semester, Teacher |
| `Task` | belongsTo: Subject, Classroom, Semester, Teacher · hasMany: TaskSubmission |
| `TaskSubmission` | belongsTo: Task, Student |

---

## Roles & Permissions

| Role | Panel Access | Description |
|---|---|---|
| `super_admin` | SIAKAD | Full access, bypasses all permission checks |
| `admin` | SIAKAD | Administrative access |
| `teacher` | Hub | Manage own content, tasks, view schedules |
| `student` | Hub | View content, submit tasks, view scores/attendance |
| `parent` | Connect | View children's academic data |

Permissions are managed by **Filament Shield** using policy-based authorization. Hub and Connect resources override `canAccess()` to bypass Shield policies (panel-level access is enforced via `User::canAccessPanel()`).

---

## Installation

### Prerequisites

- Docker & Docker Compose
- Local DNS or `/etc/hosts` entry for `maleo.test`
- SSL certificates in `nginx/ssl/` (`maleo.test.crt`, `maleo.test.key`)

### Setup

```bash
# 1. Clone the repository
git clone <repository-url> maleo
cd maleo

# 2. Start containers
docker compose up -d

# 3. Install PHP dependencies
docker compose exec maleo_php composer install

# 4. Copy environment file
docker compose exec maleo_php cp .env.example .env

# 5. Generate application key
docker compose exec maleo_php php artisan key:generate

# 6. Run migrations
docker compose exec maleo_php php artisan migrate

# 7. Seed the database
docker compose exec maleo_php php artisan db:seed

# 8. Generate Shield permissions
docker compose exec maleo_php php artisan shield:generate --all

# 9. Install & build frontend assets
docker compose exec maleo_php npm install
docker compose exec maleo_php npm run build

# 10. Create storage symlink
docker compose exec maleo_php php artisan storage:link
```

### Add to /etc/hosts

```
127.0.0.1 maleo.test
```

---

## Seeded Accounts

### Users (password: `password` for all)

| Email | Name | Role | Panel |
|---|---|---|---|
| `admin@admin.com` | Super Admin | super_admin | SIAKAD (`/admin`) |
| `teacher@maleo.test` | Jefry Sunupurwa Asri | teacher | Hub (`/hub`) |
| `student@maleo.test` | Aisyah Putri | student | Hub (`/hub`) |
| `parent@maleo.test` | Ahmad Hidayat | parent | Connect (`/connect`) |

### Master Data

| Data | Seeded Values |
|---|---|
| **Academic Year** | 2025/2026 (active) |
| **Semesters** | Semester 1 (Ganjil), Semester 2 (Genap — active) |
| **Grades** | Kelas VII, Kelas VIII, Kelas IX |
| **Classrooms** | VII-A, VII-B, VIII-A, VIII-B, IX-A, IX-B |
| **Subjects** | Matematika, Bahasa Indonesia, Bahasa Inggris, IPA, IPS, PKn, PAI, PJOK, Seni Budaya, Informatika |
| **Teacher Assignments** | Teacher → Matematika, IPA, Informatika |
| **Student Placement** | Aisyah Putri → VII-A (2025/2026) |
| **Guardian Link** | Ahmad Hidayat → Aisyah Putri (father) |
| **Schedules** | Full week timetable for VII-A (Monday–Friday, Semester 2) |

---

## Project Structure

```
maleo/
├── docker-compose.yml          # Container orchestration
├── db/                         # MariaDB config & data
│   └── conf.d/my.cnf
├── nginx/                      # Nginx config & SSL
│   ├── Dockerfile
│   ├── default.conf
│   └── ssl/
├── php/                        # PHP-FPM config
│   ├── Dockerfile
│   ├── docker-entrypoint.sh
│   ├── local.ini
│   └── www.conf
└── src/                        # Laravel application
    ├── app/
    │   ├── Filament/
    │   │   ├── Admin/Resources/       # 13 SIAKAD resources
    │   │   ├── Hub/Resources/         # 6 Hub resources
    │   │   └── Connect/Resources/     # 5 Connect resources
    │   ├── Models/                    # 18 Eloquent models
    │   ├── Policies/                  # Shield-generated policies
    │   └── Providers/Filament/
    │       ├── AdminPanelProvider.php
    │       ├── HubPanelProvider.php
    │       └── ConnectPanelProvider.php
    ├── config/
    ├── database/
    │   ├── migrations/
    │   │   ├── 2025_04_19_000001_create_school_management_tables.php
    │   │   └── 2025_04_19_000002_create_content_and_tasks_tables.php
    │   └── seeders/
    │       ├── DatabaseSeeder.php
    │       ├── RoleSeeder.php
    │       ├── UserSeeder.php
    │       └── MasterDataSeeder.php
    ├── resources/
    │   └── css/filament/
    │       ├── admin/theme.css
    │       ├── hub/theme.css
    │       └── connect/theme.css
    └── vite.config.js
```

---

## Docker Containers

| Container | Image | Ports | Purpose |
|---|---|---|---|
| `maleo_php` | php:8.3-fpm (custom) | 9000 (internal) | Application server |
| `maleo_nginx` | nginx:stable-alpine | 80, 443 | Web server with SSL |
| `maleo_db` | mariadb:10.11 | 13306 → 3306 | Database server |

### Useful Commands

```bash
# Artisan commands
docker compose exec maleo_php php artisan migrate
docker compose exec maleo_php php artisan db:seed
docker compose exec maleo_php php artisan db:seed --class=MasterDataSeeder
docker compose exec maleo_php php artisan shield:generate --all
docker compose exec maleo_php php artisan migrate:fresh --seed

# Container management
docker compose up -d
docker compose down
docker compose logs -f maleo_php

# Database access
docker compose exec maleo_db mysql -u root -pp455w0rd maleo
```
