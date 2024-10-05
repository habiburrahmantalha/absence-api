# Absence Manager API

This is a Laravel project for absence manager api.

## Prerequisites

Ensure that your system has the following installed:
- **PHP** >= 8.1

### 1. Clone the Repository

Clone the project from GitHub to your local machine using the following command:

```bash
git clone https://github.com/habiburrahmantalha/absence-api.git
cd absence-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Environment Variables

```bash
cp .env.example .env
```

- Add key into env file
- API_TOKEN=A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V2W3X4Y5Z6A7B8C9D0E1F2G3

### 4. Run 

```bash
php artisan key:generate

sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

php artisan serve
```
