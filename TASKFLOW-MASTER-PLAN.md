# 🚀 Baseline IT Development Pvt Ltd — Project & Task Management System
## Master Development Plan

> **Company:** Baseline IT Development Pvt Ltd
> **System:** Internal Project & Task Management System (Baseline TaskFlow)
> **Stack:** Laravel 12 · PHP 8.2 · MySQL · Bootstrap 5 · jQuery · Chart.js · Bootstrap Icons
> **Environment:** XAMPP · Git · Blade Templates
> **Current Project:** `c:\xampp\htdocs\LaravelProject` *(Laravel 12.72.0 · PHP 8.2.12)*

---

## 📋 Table of Contents

1. [Project Overview](#1-project-overview)
2. [Current Project Status](#2-current-project-status)
3. [User Roles & Access Control](#3-user-roles--access-control)
4. [Color System & UI Design](#4-color-system--ui-design)
5. [Database Architecture](#5-database-architecture)
6. [Application Modules](#6-application-modules)
7. [Phase-by-Phase Development Plan](#7-phase-by-phase-development-plan)
8. [File & Folder Structure](#8-file--folder-structure)
9. [Route Architecture](#9-route-architecture)
10. [Security Checklist](#10-security-checklist)
11. [Installation Guide](#11-installation-guide)

---

## 1. Project Overview

**TaskFlow** is a full-featured, role-based Project & Task Management System built with Laravel 12. It enables organizations to plan projects, assign tasks, track progress, collaborate through comments, and generate analytical reports — all from one central platform.

```
Application Name : TaskFlow
Database Name    : taskflow_db
Base URL         : http://localhost/LaravelProject/public
Laravel Version  : 12.x (already installed — v12.72.0)
PHP Version      : 8.2.12 (XAMPP)
```

---

## 2. Current Project Status

| Item | Status | Notes |
|------|--------|-------|
| Laravel 12 installed | ✅ Done | v12.72.0 |
| PHP 8.2 | ✅ Done | v8.2.12 |
| MySQL configured | ✅ Done | Currently DB: `client_portal` → will change to `taskflow_db` |
| Landing page (basic) | ✅ Done | WebTech theme — needs TaskFlow rebrand |
| Bootstrap 5 loaded | ✅ Done | Via CDN |
| Poppins Font | ✅ Done | Via Google Fonts |
| Color system | ✅ Done | `--brand: #0F66A8` → will upgrade to `#635BFF` purple |
| Auth system | ❌ Not started | Need to implement |
| Role system | ❌ Not started | Need to implement |
| Dashboards | ❌ Not started | All 4 dashboards needed |
| All modules | ❌ Not started | Full development ahead |

---

## 3. User Roles & Access Control

TaskFlow uses **4 user roles** — each gets its own dashboard, sidebar, and permission set.

```
┌─────────────────────────────────────────────────────────────┐
│                    USER ROLE HIERARCHY                       │
│                                                             │
│   👑 ADMIN            → Full system access                  │
│   📋 PROJECT MANAGER  → Assigned projects + their tasks     │
│   👥 TEAM LEADER      → Sub-team tasks + team reporting     │
│   👤 EMPLOYEE         → Own assigned tasks only             │
└─────────────────────────────────────────────────────────────┘
```

### Role Permission Matrix

| Feature | Admin | Project Manager | Team Leader | Employee |
|---------|-------|-----------------|-------------|----------|
| Manage Users | ✅ | ❌ | ❌ | ❌ |
| Create Projects | ✅ | ✅ | ❌ | ❌ |
| View All Projects | ✅ | ❌ | ❌ | ❌ |
| View Assigned Projects | ✅ | ✅ | ✅ | ✅ |
| Create Tasks | ✅ | ✅ | ✅ | ❌ |
| Assign Tasks | ✅ | ✅ | ✅ (sub-team) | ❌ |
| Update Task Status | ✅ | ✅ | ✅ | ✅ (own tasks) |
| View All Tasks | ✅ | ❌ | ❌ | ❌ |
| View Project Tasks | ✅ | ✅ | ✅ | ✅ (own only) |
| Approve / Review Tasks | ✅ | ✅ | ✅ | ❌ |
| Add Comments | ✅ | ✅ | ✅ | ✅ |
| Upload Attachments | ✅ | ✅ | ✅ | ✅ |
| View Reports | ✅ | ✅ (own) | ✅ (team) | ❌ |
| Export PDF / Excel | ✅ | ✅ | ✅ | ❌ |
| System Settings | ✅ | ❌ | ❌ | ❌ |
| Manage Notifications | ✅ | ✅ | ✅ | ✅ |

### Role Redirect After Login

```
Admin           → /admin/dashboard
Project Manager → /pm/dashboard
Team Leader     → /tl/dashboard
Employee        → /employee/dashboard
```

---

## 4. Color System & UI Design

### Primary Color Palette (TaskFlow)

```css
:root {
  /* Primary Brand */
  --primary:        #635BFF;   /* Purple-Blue — buttons, accents */
  --primary-dark:   #4F46E5;   /* Hover state */
  --primary-light:  #EEF2FF;   /* Light backgrounds, badges */

  /* Backgrounds */
  --bg-main:        #F7F8FC;   /* Page background */
  --bg-card:        #FFFFFF;   /* Card background */
  --bg-sidebar:     #1E1B4B;   /* Sidebar dark */

  /* Text */
  --text-dark:      #1E293B;   /* Primary text */
  --text-muted:     #64748B;   /* Secondary text */
  --text-light:     #CBD5E1;   /* Placeholder */

  /* Status Colors */
  --success:        #22C55E;   /* Completed */
  --warning:        #F59E0B;   /* Pending / In Progress */
  --danger:         #EF4444;   /* Overdue / Urgent */
  --info:           #3B82F6;   /* Information */

  /* Priority Colors */
  --priority-low:    #22C55E;
  --priority-medium: #F59E0B;
  --priority-high:   #EF4444;
  --priority-urgent: #DC2626;

  /* Sidebar */
  --sidebar-width:  260px;
  --sidebar-bg:     #1E1B4B;
  --sidebar-hover:  #312E81;
  --sidebar-active: #635BFF;
  --sidebar-text:   #C7D2FE;
}
```

### Typography

```
Font Family : Poppins (already loaded via Google Fonts)
Headings    : 600–700 weight
Body        : 400–500 weight
Size Scale  : 12px / 14px / 16px / 20px / 24px / 32px
```

### Component Design Rules

| Component | Style |
|-----------|-------|
| Cards | `border-radius: 12px`, white bg, `box-shadow: 0 1px 3px rgba(0,0,0,0.08)` |
| Buttons | `border-radius: 8px`, primary purple fill or outline |
| Inputs | `border-radius: 8px`, subtle border `#E2E8F0` |
| Badges | Rounded pill, color-coded by status/priority |
| Sidebar | Dark `#1E1B4B`, active items highlighted purple `#635BFF` |
| Tables | Striped, hover row highlight, responsive |
| Charts | Chart.js — Doughnut + Bar + Line |

---

## 5. Database Architecture

### Tables Required

#### `users`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | Auto increment |
| name | varchar(100) | Full name |
| email | varchar(150) | Unique |
| phone | varchar(20) | Nullable |
| password | varchar(255) | Hashed |
| role | enum | `admin`, `project_manager`, `team_leader`, `employee` |
| designation | varchar(100) | Job title |
| joining_date | date | Nullable |
| profile_image | varchar(255) | Nullable |
| status | enum | `active`, `inactive` |
| email_verified_at | timestamp | Nullable |
| remember_token | varchar(100) | Nullable |
| timestamps | | created_at / updated_at |
| deleted_at | timestamp | Soft delete |

#### `projects`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| name | varchar(200) | |
| description | text | Nullable |
| project_manager_id | FK → users | Nullable |
| start_date | date | |
| deadline | date | |
| priority | enum | `low`, `medium`, `high`, `urgent` |
| status | enum | `planning`, `active`, `on_hold`, `completed`, `cancelled` |
| created_by | FK → users | Admin who created |
| timestamps | | |
| deleted_at | timestamp | Soft delete |

#### `project_members`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| project_id | FK → projects | |
| user_id | FK → users | |
| role_in_project | enum | `manager`, `team_leader`, `member` |
| joined_at | timestamp | |

#### `tasks`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| title | varchar(200) | |
| description | text | Nullable |
| project_id | FK → projects | |
| assigned_to | FK → users | Nullable |
| created_by | FK → users | |
| start_date | date | Nullable |
| due_date | date | Nullable |
| priority | enum | `low`, `medium`, `high`, `urgent` |
| status | enum | `todo`, `in_progress`, `in_review`, `completed`, `on_hold` |
| timestamps | | |
| deleted_at | timestamp | Soft delete |

#### `task_comments`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| task_id | FK → tasks | |
| user_id | FK → users | |
| parent_id | FK → task_comments | Nullable (reply) |
| comment | text | |
| timestamps | | |

#### `task_attachments`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| task_id | FK → tasks | |
| uploaded_by | FK → users | |
| file_name | varchar(255) | Original name |
| file_path | varchar(255) | Storage path |
| file_type | varchar(50) | MIME type |
| file_size | bigint | Bytes |
| timestamps | | |

#### `task_activities`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| task_id | FK → tasks | |
| user_id | FK → users | |
| action | varchar(255) | e.g. "changed status to Completed" |
| old_value | varchar(255) | Nullable |
| new_value | varchar(255) | Nullable |
| created_at | timestamp | |

#### `notifications`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| user_id | FK → users | Recipient |
| type | varchar(100) | e.g. `task_assigned`, `deadline_approaching` |
| title | varchar(255) | |
| message | text | |
| related_id | bigint | Nullable |
| related_type | varchar(100) | Nullable |
| is_read | boolean | Default: false |
| created_at | timestamp | |

---

## 6. Application Modules

```
TaskFlow
├── 🌐 Public Landing Page
│   ├── Navbar + Logo (TaskFlow)
│   ├── Hero Section
│   ├── Features Section (6 cards)
│   ├── How It Works (4 steps)
│   ├── About Us
│   ├── Call to Action
│   └── Footer
│
├── 🔐 Authentication
│   ├── Login
│   ├── Logout
│   ├── Forgot Password
│   ├── Reset Password
│   └── Change Password
│
├── 👑 Admin Panel
│   ├── Dashboard (stats + charts)
│   ├── Employee Management (CRUD)
│   ├── Project Management (CRUD)
│   ├── Task Management (CRUD)
│   ├── Reports & Analytics
│   ├── Notifications
│   ├── My Profile
│   └── Settings
│
├── 📋 Project Manager Panel
│   ├── Dashboard
│   ├── My Projects
│   ├── Team Members
│   ├── Task Management
│   ├── Task Assignments
│   ├── Pending Reviews
│   ├── Reports
│   └── Notifications
│
├── 👥 Team Leader Panel
│   ├── Dashboard
│   ├── My Projects
│   ├── My Team
│   ├── Team Tasks
│   ├── Task Assignments (sub-team)
│   ├── Pending Reviews
│   └── Notifications
│
└── 👤 Employee Panel
    ├── Dashboard
    ├── My Projects
    ├── My Tasks
    ├── Pending Tasks
    ├── Completed Tasks
    └── Notifications
```

---

## 7. Phase-by-Phase Development Plan

---

### 🔵 PHASE 1 — Foundation Setup
**Goal:** Configure the project, database, models, auth, and roles.

**Checklist:**
- [ ] Update `.env` → `APP_NAME=TaskFlow`, `DB_DATABASE=taskflow_db`
- [ ] Create MySQL database `taskflow_db` in phpMyAdmin
- [ ] Install required packages (laravel/ui, dompdf, maatwebsite/excel)
- [ ] Create all migrations (users modify + 7 new tables)
- [ ] Run `php artisan migrate`
- [ ] Create all Eloquent Models with relationships
- [ ] Create `RoleMiddleware.php` and `CheckActive.php`
- [ ] Register middleware aliases in `bootstrap/app.php`
- [ ] Create seeders (Admin + PM + TL + Employee + projects + tasks)
- [ ] Run `php artisan db:seed`
- [ ] Configure login redirect by role

**Commands:**
```bash
composer require laravel/ui
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
php artisan ui bootstrap --auth
php artisan migrate
php artisan db:seed
php artisan storage:link
```

---

### 🟢 PHASE 2 — Public Landing Page + Layouts
**Goal:** Rebrand landing page to TaskFlow + build all 4 dashboard shell layouts.

**Checklist:**
- [ ] Update landing page branding (WebTech → TaskFlow)
- [ ] Upgrade CSS color palette to `#635BFF` purple system
- [ ] Build/update all landing page sections (Hero, Features, How It Works, About, CTA, Footer)
- [ ] Create `layouts/admin.blade.php`
- [ ] Create `layouts/pm.blade.php`
- [ ] Create `layouts/tl.blade.php`
- [ ] Create `layouts/employee.blade.php`
- [ ] Create sidebars for each role
- [ ] Create topnavs for each role (notification bell + profile)

---

### 🟡 PHASE 3 — Admin Dashboard + Employee + Project Management
**Goal:** Live admin stats, full employee CRUD, full project CRUD.

**Checklist:**
- [ ] Admin dashboard with 8 stats cards (live from DB)
- [ ] Project Status doughnut chart (Chart.js)
- [ ] Task Status bar chart (Chart.js)
- [ ] Recent activities table
- [ ] Upcoming deadlines table
- [ ] Employee Management: list, create, edit, view, activate/deactivate, delete
- [ ] Project Management: list, create, edit, view, assign PM, assign members, archive

---

### 🟠 PHASE 4 — Task Management + Comments + Attachments
**Goal:** Core task module with full collaboration features.

**Checklist:**
- [ ] Task CRUD (create, edit, view, delete)
- [ ] Task filters (project, employee, status, priority, date)
- [ ] Task status flow: To Do → In Progress → In Review → Completed
- [ ] Task activity log on every status change
- [ ] Task comments (add + reply + display threaded)
- [ ] File attachments (upload, download, delete)
- [ ] AJAX comment submission (no page reload)

---

### 🔴 PHASE 5 — PM + Team Leader + Employee Dashboards
**Goal:** Build the remaining 3 dashboards with correct data scoping.

**Checklist:**
- [ ] Project Manager dashboard (scoped to their projects)
- [ ] PM: My Projects, Team Members, Task Assignments, Pending Reviews
- [ ] Team Leader dashboard (scoped to their sub-team)
- [ ] TL: My Team, Team Tasks, Task Assign to sub-team, Pending Reviews
- [ ] Employee dashboard (scoped to own tasks only)
- [ ] Employee: My Tasks, Pending, Completed, Upcoming Deadlines

---

### 🟣 PHASE 6 — Notifications + Reports & Analytics
**Goal:** System-wide notifications + reporting with PDF/Excel export.

**Checklist:**
- [ ] NotificationHelper to send on: task assigned, deadline near, status change, review submitted, approved, comment added
- [ ] Bell icon in topnav with live unread count badge
- [ ] Notifications dropdown list
- [ ] Mark as Read / Mark All Read
- [ ] Admin Reports: project summary, employee performance, overdue tasks
- [ ] PM Reports: project-wise task status, employee task progress
- [ ] TL Reports: team task summary
- [ ] Date / project / status filters on all reports
- [ ] PDF export (DomPDF)
- [ ] Excel export (Maatwebsite)

---

### ⚫ PHASE 7 — Testing + Bug Fix + Final Polish
**Goal:** Test everything, fix bugs, ensure security and responsiveness.

**Checklist:**
- [ ] Test role-based access (try accessing wrong role URLs)
- [ ] Test URL ID tampering (try editing other user's records by changing ID)
- [ ] Test deactivated user login (must be blocked)
- [ ] Test all forms (server-side validation)
- [ ] Test file upload (type + size limits)
- [ ] Test date validation (due date < start date)
- [ ] Mobile / tablet responsive check
- [ ] All charts load with real data
- [ ] Empty states shown when no data
- [ ] All notifications triggering correctly

---

## 8. File & Folder Structure

```
LaravelProject/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   ├── AuthController.php
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── EmployeeController.php
│   │   │   │   ├── ProjectController.php
│   │   │   │   ├── TaskController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   └── SettingsController.php
│   │   │   ├── PM/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ProjectController.php
│   │   │   │   ├── TaskController.php
│   │   │   │   └── ReportController.php
│   │   │   ├── TL/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── TaskController.php
│   │   │   │   └── TeamController.php
│   │   │   ├── Employee/
│   │   │   │   ├── DashboardController.php
│   │   │   │   └── TaskController.php
│   │   │   ├── TaskCommentController.php
│   │   │   ├── TaskAttachmentController.php
│   │   │   ├── NotificationController.php
│   │   │   └── ProfileController.php
│   │   ├── Middleware/
│   │   │   ├── RoleMiddleware.php
│   │   │   └── CheckActive.php
│   │   └── Requests/
│   │       ├── StoreEmployeeRequest.php
│   │       ├── StoreProjectRequest.php
│   │       ├── StoreTaskRequest.php
│   │       └── StoreCommentRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Project.php
│   │   ├── ProjectMember.php
│   │   ├── Task.php
│   │   ├── TaskComment.php
│   │   ├── TaskAttachment.php
│   │   ├── TaskActivity.php
│   │   └── Notification.php
│   ├── Helpers/
│   │   └── NotificationHelper.php
│   └── Exports/
│       ├── ProjectsExport.php
│       ├── TasksExport.php
│       └── EmployeePerformanceExport.php
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── UserSeeder.php
│   │   ├── ProjectSeeder.php
│   │   └── TaskSeeder.php
│   └── factories/
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   ├── admin.blade.php
│       │   ├── pm.blade.php
│       │   ├── tl.blade.php
│       │   └── employee.blade.php
│       ├── partials/
│       │   ├── header.blade.php
│       │   ├── footer.blade.php
│       │   ├── admin/sidebar.blade.php
│       │   ├── admin/topnav.blade.php
│       │   ├── pm/sidebar.blade.php
│       │   ├── pm/topnav.blade.php
│       │   ├── tl/sidebar.blade.php
│       │   ├── tl/topnav.blade.php
│       │   └── employee/sidebar.blade.php
│       ├── home/
│       │   └── sections/
│       │       ├── hero.blade.php
│       │       ├── features.blade.php
│       │       ├── how-it-works.blade.php
│       │       ├── about.blade.php
│       │       ├── cta.blade.php
│       │       └── contact.blade.php
│       ├── auth/
│       │   ├── login.blade.php
│       │   ├── forgot-password.blade.php
│       │   └── reset-password.blade.php
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── employees/
│       │   ├── projects/
│       │   ├── tasks/
│       │   ├── reports/
│       │   └── settings/
│       ├── pm/
│       │   ├── dashboard.blade.php
│       │   ├── projects/
│       │   ├── tasks/
│       │   └── reports/
│       ├── tl/
│       │   ├── dashboard.blade.php
│       │   ├── tasks/
│       │   └── team/
│       ├── employee/
│       │   ├── dashboard.blade.php
│       │   └── tasks/
│       └── shared/
│           ├── notifications.blade.php
│           └── profile.blade.php
│
├── routes/
│   ├── web.php
│   └── auth.php
│
├── public/
│   └── assets/
│       ├── css/style.css
│       ├── js/main.js
│       └── images/
│
└── TASKFLOW-MASTER-PLAN.md   ← THIS FILE
```

---

## 9. Route Architecture

```php
// Public
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', ...)->name('password.request');
Route::post('/forgot-password', ...)->name('password.email');
Route::get('/reset-password/{token}', ...)->name('password.reset');
Route::post('/reset-password', ...)->name('password.update');

// Admin → middleware: auth, role:admin, active
Route::prefix('admin')->middleware(['auth', 'role:admin', 'active'])->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index']);
    Route::resource('employees', Admin\EmployeeController::class);
    Route::resource('projects', Admin\ProjectController::class);
    Route::resource('tasks', Admin\TaskController::class);
    Route::get('/reports', [Admin\ReportController::class, 'index']);
});

// Project Manager → middleware: auth, role:project_manager, active
Route::prefix('pm')->middleware(['auth', 'role:project_manager', 'active'])->group(function () {
    Route::get('/dashboard', [PM\DashboardController::class, 'index']);
    Route::resource('projects', PM\ProjectController::class);
    Route::resource('tasks', PM\TaskController::class);
    Route::get('/reports', [PM\ReportController::class, 'index']);
});

// Team Leader → middleware: auth, role:team_leader, active
Route::prefix('tl')->middleware(['auth', 'role:team_leader', 'active'])->group(function () {
    Route::get('/dashboard', [TL\DashboardController::class, 'index']);
    Route::resource('tasks', TL\TaskController::class);
    Route::get('/team', [TL\TeamController::class, 'index']);
});

// Employee → middleware: auth, role:employee, active
Route::prefix('employee')->middleware(['auth', 'role:employee', 'active'])->group(function () {
    Route::get('/dashboard', [Employee\DashboardController::class, 'index']);
    Route::resource('tasks', Employee\TaskController::class);
});

// Shared → all authenticated users
Route::middleware(['auth', 'active'])->group(function () {
    Route::resource('tasks.comments', TaskCommentController::class);
    Route::resource('tasks.attachments', TaskAttachmentController::class);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/password', [ProfileController::class, 'changePassword']);
});
```

---

## 10. Security Checklist

| Security Measure | Implementation |
|-----------------|----------------|
| CSRF Protection | `@csrf` on all forms |
| Password Hashing | `bcrypt()` via Laravel Hash |
| SQL Injection Prevention | Eloquent ORM (parameterized) |
| Mass Assignment Protection | `$fillable` on all models |
| Authorization | Role middleware + Laravel Policies |
| URL ID Tampering | Policy check on every action |
| File Upload Validation | Type + size check in Form Request |
| Inactive User Blocking | `CheckActive` middleware |
| XSS Prevention | Blade `{{ }}` auto-escaping |
| Sensitive Routes | All dashboard routes behind `auth` middleware |

---

## 11. Installation Guide

```bash
# 1. Navigate to project
cd c:\xampp\htdocs\LaravelProject

# 2. Create database in phpMyAdmin
# Database name: taskflow_db

# 3. Update .env
APP_NAME=TaskFlow
DB_DATABASE=taskflow_db
DB_USERNAME=root
DB_PASSWORD=

# 4. Install packages
composer install
composer require laravel/ui
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel

# 5. Run migrations
php artisan migrate

# 6. Seed sample data
php artisan db:seed

# 7. Create storage link
php artisan storage:link

# 8. Clear caches
php artisan config:clear
php artisan cache:clear

# 9. Open browser
# http://localhost/LaravelProject/public
```

### Default Login Credentials (after seeding)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@baselineitdevelopment.com | password |
| Project Manager | pm@baselineitdevelopment.com | password |
| Team Leader | tl@baselineitdevelopment.com | password |
| Employee | employee@baselineitdevelopment.com | password |

---

## 📅 Development Order (Step by Step)

```
NEXT STEP TO DO FIRST:
─────────────────────
Step 1 → Create database `taskflow_db` in phpMyAdmin
Step 2 → Update .env file (APP_NAME, DB_DATABASE)
Step 3 → Install packages (laravel/ui, dompdf, excel)
Step 4 → Modify users migration (add role, phone, designation etc.)
Step 5 → Create all 7 new migrations
Step 6 → php artisan migrate
Step 7 → Create all 8 Models with relationships
Step 8 → Create RoleMiddleware + CheckActive middleware
Step 9 → Create Auth routes + login/logout + role redirect
Step 10 → Create seeders → php artisan db:seed
─────────────────────
(Phase 1 complete — then move to Phase 2)
```

---

*TaskFlow Master Plan v1.0*
*Created: November 2026*
*Laravel 12.72.0 · PHP 8.2.12 · MySQL · Bootstrap 5*
