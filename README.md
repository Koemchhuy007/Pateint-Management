# Patient Management System

A Laravel web application for storing and managing patient information. Includes full CRUD operations, search, filtering, and a clean healthcare-oriented UI.

## Features

- **Patient records**: Store personal info, contact details, emergency contacts
- **Search & filter**: Find patients by name, ID, email, phone, or status
- **Full CRUD**: Create, read, update, delete patient records
- **Patient fields**: ID, name, DOB, gender, blood type, address, medical notes, insurance info

## Requirements

- PHP 8.2 or higher
- Composer
- SQLite (default) or MySQL

## Installation

### 1. Install Composer (if needed)

Download and install Composer from [getcomposer.org](https://getcomposer.org/). On Windows, you can use the installer or Chocolatey:

```bash
choco install composer
```

### 2. Install Dependencies

```bash
cd "d:\laravel with Cursor"
composer install
```

### 3. Environment Setup

```bash
copy .env.example .env
php artisan key:generate
```

### 4. Database Setup

**SQLite (default):**
```bash
# Create empty database file
type nul > database\database.sqlite

# Run migrations
php artisan migrate
```

**MySQL:** Edit `.env` and set:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=patient_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

Then run `php artisan migrate`.

### 5. Start the Server

```bash
php artisan serve
```

Open [http://localhost:8000](http://localhost:8000) in your browser.

## Optional: Sample Data

To seed sample patients for testing:

```bash
php artisan tinker
>>> App\Models\Patient::factory()->count(10)->create();
>>> exit
```

## Project Structure

```
app/
├── Http/Controllers/PatientController.php
├── Models/Patient.php
database/
├── migrations/
│   └── 2024_02_17_000000_create_patients_table.php
├── factories/PatientFactory.php
resources/views/
├── layouts/app.blade.php
└── patients/
    ├── index.blade.php    (list + search)
    ├── create.blade.php
    ├── edit.blade.php
    ├── show.blade.php
    └── _form.blade.php
routes/web.php
```

## License

MIT
