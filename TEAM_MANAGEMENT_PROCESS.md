# TaskFlow - Team & Role Management Documentation

## 1. User Roles & Hierarchy
| Role | Database Enum | Dashboard URL | Primary Purpose |
|------|---------------|---------------|-----------------|
| **Administrator** | `admin` | `/admin/dashboard` | Full system command, staff management, organization overview |
| **Project Manager** | `project_manager` | `/pm/dashboard` | Projects, sprints, timelines, milestone reviews |
| **Team Leader** | `team_leader` | `/tl/dashboard` | Sub-teams, task allocation, code/PR reviews |
| **Employee** | `employee` | `/employee/dashboard` | Developers, QA Testers, UI/UX Designers task execution |

> **Note on Employee Role:** Base role `employee` rakha gaya hai taaki Developers ke alawa QA aur Designers bhi isi portal mein manage ho sakein. Unka exact role **Designation** field se define hota hai.

---

## 2. Admin Creation Workflow

### A. Quick Action URLs
- **All Team Members Directory:** `/admin/users`
- **Create Project Manager:** `/admin/users/create?role=project_manager`
- **Create Team Leader:** `/admin/users/create?role=team_leader`
- **Create Employee:** `/admin/users/create?role=employee`

### B. Required Fields for Creation
1. **Full Name** (`name`) - Member ka naam
2. **Email Address** (`email`) - Unique login email
3. **Role** (`role`) - `project_manager`, `team_leader`, ya `employee`
4. **Designation** (`designation`) - Jaise *Backend Developer*, *QA Lead*, *UI Designer*
5. **Initial Password** (`password`) - Login ke liye (Min 6 chars)
6. **Joining Date** (`joining_date`) - Default: Current date
7. **Account Status** (`status`) - `active` / `inactive`

---

## 3. Login & Automatic Redirection Flow

1. User `/login` par jata hai aur credentials enter karta hai.
2. `AuthController` status verify karta hai (Inactive users login nahi kar sakte).
3. Authenticate hone ke baad system role-specific dashboard par redirect karta hai:
   - Admin &rarr; `/admin/dashboard`
   - PM &rarr; `/pm/dashboard`
   - TL &rarr; `/tl/dashboard`
   - Employee &rarr; `/employee/dashboard`
4. Security: `RoleMiddleware` ensure karta hai ki koi bhi role dusre role ke private dashboard ko access na kar sake.

---

## 4. Key Files Reference

- **Controller:** `app/Http/Controllers/Admin/UserController.php`
- **Routes:** `routes/web.php` (Prefix: `admin/users`)
- **Views:**
  - Directory: `resources/views/admin/users/index.blade.php`
  - Create Form: `resources/views/admin/users/create.blade.php`
  - Edit Form: `resources/views/admin/users/edit.blade.php`
  - Admin Dashboard: `resources/views/admin/dashboard.blade.php`
- **Styling:** `public/assets/css/dashboard.css` (Clean Pearl Light Theme)
- **Tests:** `tests/Feature/AdminUserManagementTest.php` (All 17 tests passing)
