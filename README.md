# Pet Health Tracker API

PHP and MySQL backend for the **Pet Health & Medical Tracker** mobile application built with MIT App Inventor.

## Features

- User registration, login, and profile management
- Pet profile management and weight tracking
- Categorised medical logs and medical history
- Veterinary appointment tracking
- Medical report summaries, weight trends, and category statistics

## Local setup

1. Install XAMPP and start **Apache** and **MySQL**.
2. Copy this repository to `C:\xampp\htdocs\pet_app_api`.
3. Create a MySQL database named `petapp_db`.
4. Import `database.sql` using phpMyAdmin.
5. Check the database credentials in `db_connect.php`.
6. In MIT App Inventor, use your computer's local IPv4 address, for example:

   `http://192.168.1.10/pet_app_api/login.php`

The phone and computer must be connected to the same network when testing with the AI Companion.

## Main endpoints

| Area | Endpoints |
|---|---|
| Authentication | `register.php`, `login.php` |
| User profile | `get_user_profile.php`, `update_user_profile.php` |
| Pets | `add_pet.php`, `get_pets.php`, `update_pet.php` |
| Medical logs | `add_health_log.php`, `get_medical_data.php`, `get_health_log_detail.php`, `update_health_log.php`, `delete_health_log.php` |
| Form options | `get_form_options.php` |
| Appointments | `add_appointment.php`, `get_appointments.php` |
| Reports | `get_report_summary.php`, `get_weight_logs.php`, `add_weight_log.php` |

## Notes

- API responses use JSON.
- Passwords are stored with PHP password hashing.
- The database uses foreign keys, so a medical log must reference an existing pet and category.
- Do not commit real passwords or production database credentials.
