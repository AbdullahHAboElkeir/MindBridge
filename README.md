# MindBridge - Holistic Mental Health & Wellness Portal

## Overview

MindBridge is a comprehensive PHP MVC web application designed as a mental health and wellness portal. It implements role-based access control with four user types: Patients, Therapists, Admins, and Clinic Managers.

## Features

### Core Modules
- **User Management**: Complete CRUD operations for user accounts
- **Authentication & Authorization**: Secure login/logout with role-based permissions
- **Clinical Intake & Matching**: Patient intake forms and therapist matching
- **Session & Scheduling**: Appointment booking and management
- **Wellness & Self-Help**: Mood tracking, journaling, and wellness resources
- **Community & Forum**: Anonymous discussion forums with moderation
- **Crisis & Emergency**: Crisis detection and intervention tracking
- **Reports**: PDF report generation for analytics
- **Notifications**: System notifications and reminders
- **File Upload**: Secure document and media uploads

### Technical Features
- **MVC Architecture**: Clean separation of concerns
- **OOP Design**: Encapsulation, inheritance, abstraction, composition
- **Singleton Pattern**: Database connection management
- **PDO Database Layer**: Secure database interactions
- **Bootstrap 5 UI**: Responsive, medical-themed interface
- **AJAX Integration**: Dynamic content updates
- **Security**: Password hashing, input validation, XSS prevention

## Technology Stack

- **Backend**: PHP 7.4+ (Object-Oriented)
- **Frontend**: HTML5, CSS3, Bootstrap 5, Vanilla JavaScript
- **Database**: MySQL 5.7+
- **Server**: Apache (XAMPP)
- **Architecture**: MVC Pattern

## Installation

### Prerequisites
- XAMPP (Apache, MySQL, PHP 7.4+)
- Web browser

### Setup Steps

1. **Install XAMPP**:
   - Download and install XAMPP from https://www.apachefriends.org/
   - Start XAMPP Control Panel

2. **Place the project**:
   ```bash
   # Copy the MindBridge folder to:
   C:\xampp\htdocs\MindBridge\
   ```

3. **Start Services**:
   - In XAMPP Control Panel, start Apache and MySQL
   - Ensure both show green status

4. **Setup Database**:
   - Open browser and go to: http://localhost/MindBridge/setup.php
   - Or manually import `database/mindbridge.sql` in phpMyAdmin

5. **Enable mod_rewrite** (optional, for clean URLs):
   - Open `C:\xampp\apache\conf\httpd.conf`
   - Find and uncomment: `LoadModule rewrite_module modules/mod_rewrite.so`
   - Restart Apache

6. **Access the Application**:
   - Open browser: http://localhost/MindBridge/
   - Default login: admin@mindbridge.com / password123

### Troubleshooting

#### Database Connection Error
- Ensure MySQL is running in XAMPP
- Check if database 'mindbridge' exists in phpMyAdmin
- Run setup.php to auto-create database

#### Page Not Found (404)
- Check if .htaccess is enabled (step 5 above)
- Or access pages directly: http://localhost/MindBridge/index.php/login

#### Assets Not Loading
- Check if paths are correct in browser dev tools
- Ensure files exist in assets/ folder

#### Permission Errors
- Ensure XAMPP has write permissions to htdocs/MindBridge/uploads/

## Database Schema

### Core Tables
- `users` - User accounts with roles
- `roles` - User role definitions
- `patients` - Patient-specific information
- `therapists` - Therapist credentials and info
- `sessions` - Therapy session bookings
- `mood_trackers` - Daily mood entries
- `journals` - Patient journal entries
- `forums` - Discussion categories
- `posts` - Forum posts
- `comments` - Post comments
- `resources` - Wellness resources

### Relationships
- Users belong to Roles (Many-to-One)
- Patients/Therapists extend Users (One-to-One)
- Sessions link Patients and Therapists (Many-to-One)
- Posts belong to Forums (Many-to-One)
- Comments belong to Posts (Many-to-One)

## MVC Architecture

### Model Layer
- Base `Model` class with CRUD operations
- Specific models extend base functionality
- Database interactions via PDO

### View Layer
- PHP templates with Bootstrap styling
- Responsive design for all devices
- Medical/healthcare color scheme

### Controller Layer
- Base `Controller` class with common methods
- Specific controllers handle business logic
- Request routing and response handling

## Security Features

- **Password Hashing**: bcrypt via `password_hash()`
- **Input Validation**: Server-side validation
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Protection**: Input sanitization
- **Session Security**: Secure session management
- **Role-Based Access**: Permission validation

## User Roles & Permissions

### Patient
- Book therapy sessions
- Track mood and wellness
- Access community forums
- View personal reports

### Therapist
- Manage session schedules
- View patient information
- Access clinical tools
- Generate reports

### Admin
- Full system management
- User account management
- System configuration
- Audit logging

### Clinic Manager
- Clinic-specific management
- Staff oversight
- Resource management

## API Endpoints

- `GET /` - Home/Dashboard
- `POST /login` - User authentication
- `POST /register` - User registration
- `GET /users` - User management (Admin)
- `GET /sessions` - Session management
- `POST /mood` - Mood tracking (AJAX)
- `GET /forum` - Community forums
- `GET /reports` - Report generation

## Development

### File Structure
```
MindBridge/
├── app/
│   ├── controllers/    # Business logic
│   ├── models/        # Data models
│   └── views/         # UI templates
├── core/              # Framework core
├── config/            # Configuration
├── public/            # Public assets
├── assets/            # CSS/JS files
├── database/          # SQL schemas
├── routes/            # URL routing
└── index.php          # Entry point
```

### Adding New Features
1. Create model in `app/models/`
2. Create controller in `app/controllers/`
3. Add route in `routes/web.php`
4. Create view in `app/views/`
5. Update database schema if needed

## Testing

### Default Test Accounts
- **Admin**: admin@mindbridge.com / password123
- **Therapist**: therapist@mindbridge.com / password123
- **Patient**: patient@mindbridge.com / password123

### Sample Data
The SQL file includes sample users and basic data for testing.

## Troubleshooting

### Common Issues
1. **Database Connection Error**
   - Check XAMPP MySQL is running
   - Run http://localhost/MindBridge/setup.php to auto-setup database
   - Or manually import database/mindbridge.sql in phpMyAdmin

2. **Page Not Found (404)**
   - Ensure Apache mod_rewrite is enabled in httpd.conf
   - Or access pages as: http://localhost/MindBridge/index.php/login
   - Check .htaccess file exists

3. **Assets Not Loading (CSS/JS)**
   - Check browser dev tools for 404 on asset files
   - Ensure paths are correct (should be /MindBridge/assets/...)
   - Check files exist in assets/ folder

4. **Permission Errors**
   - Ensure XAMPP has write permissions to uploads/ folder
   - Check folder permissions in Windows

5. **AJAX Not Working**
   - Check browser console for JavaScript errors
   - Ensure XMLHttpRequest headers are set correctly
   - Check PHP error logs

6. **Login Not Working**
   - Verify database has user records
   - Check password hashing (uses bcrypt)
   - Default admin: admin@mindbridge.com / password123

### Error Logs
- Check XAMPP Apache error logs: `C:\xampp\apache\logs\error.log`
- PHP errors are displayed if enabled in index.php

## Future Enhancements

- Email notifications
- Video conferencing integration
- Mobile app API
- Advanced analytics
- Multi-language support
- Payment integration

## License

This project is for educational purposes as part of a software engineering curriculum.

## Contributing

This is an academic project. For improvements, please follow standard PHP development practices and maintain MVC architecture.