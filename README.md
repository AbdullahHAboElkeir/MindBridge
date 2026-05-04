# MindBridge

MindBridge is a university-level mental health and wellness portal built with native PHP, MySQL, XAMPP, MVC architecture, Bootstrap frontend, PDO connectivity, PHP sessions, and role-based authentication.

## Project Structure

- `app/controllers/` - Controller classes handling user requests and business logic.
- `app/models/` - Domain models and service objects for data access.
- `app/views/` - Bootstrap UI templates and pages.
- `core/` - Framework classes, including the Singleton database manager and authentication helper.
- `config/` - Application configuration (`config.php`).
- `assets/` - CSS and JS for the frontend.
- `uploads/` - Secure upload storage for documents.
- `database/schema.sql` - MySQL schema and seed data.
- `index.php` - Front controller entry point.

## Features

- Role-based authentication for `patient`, `therapist`, and `admin`
- Secure login and registration with `password_hash()` and `password_verify()`
- MVC architecture with clear separation of controllers, models, and views
- Appointment booking and scheduling with conflict prevention
- Mood tracking with AJAX updates
- Anonymous forum posting and comment workflows
- Resource management for wellness materials
- Secure file uploads with validation and safe naming
- Audit logging, reports, and admin dashboards
- Normalized MySQL schema with foreign keys and relationships

## Setup Instructions

1. Place the `MindBridgenew` folder inside `C:\xampp\htdocs\`.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Create a new database named `mindbridge` or run the `database/schema.sql` file directly.
5. Import `database/schema.sql` into MySQL.
6. If needed, update `config/config.php` with your MySQL credentials.
7. Access the application at `http://localhost/MindBridgenew/`.

## Default Accounts

- Admin: `admin@mindbridge.local` / `Admin123!`
- Patient: `patient@mindbridge.local` / `Admin123!`
- Therapist: `therapist@mindbridge.local` / `Admin123!`

## MVC Explanation

- `index.php` is the front controller. It routes requests to a controller class and action method.
- Controllers take user input, call models for data operations, and render views.
- Models encapsulate data logic, database access, and business rules.
- Views generate responsive UI using Bootstrap and consume data passed by controllers.

## Database Schema Highlights

- `users` stores all users and roles.
- `patient_profiles` and `therapist_profiles` implement one-to-one profile relationships.
- `appointments`, `sessions`, and `payments` model therapy scheduling and billing.
- `mood_entries`, `journal_entries`, `forums`, `posts`, and `comments` support wellness tracking and community interaction.
- `audit_logs` records user actions for security and compliance.

## Notes

- No Laravel, React, or external frontend frameworks were used.
- All business logic is implemented with object-oriented PHP and MVC structure.
- The project is designed for local XAMPP deployment and academic submission.
