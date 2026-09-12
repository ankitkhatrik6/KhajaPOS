<div align="center">

<img src="public/logo.png" alt="KhajaPOS logo" width="150">

# KhajaPOS

### Restaurant Point of Sale, Inventory and Billing System

[![PHP](https://img.shields.io/badge/PHP-8.2-7c3aed)](composer.json)
[![Laravel](https://img.shields.io/badge/Framework-Laravel%2012-4b0082)](composer.json)
[![Database](https://img.shields.io/badge/Database-MySQL%2FMariaDB-00758f)](config/database.php)
[![License](https://img.shields.io/badge/License-MIT-brightgreen)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Active-2ea043)](routes/web.php)

</div>

## About

KhajaPOS is a production-ready restaurant point of sale, stock management and billing platform built with PHP and the Laravel framework. It is designed for real-world restaurants and cafes that need a fast cash counter, an accurate inventory ledger and clean VAT-compliant receipts all in one place.

The system runs entirely on MySQL or MariaDB and is served by PHP, which makes it an excellent fit for local deployment with XAMPP on a single machine or across a small office network. All transactions are recorded in NPR and every sale automatically deducts stock in real time.

## Features

- Point of Sale terminal with instant item search and category filters
- Cash and online payment methods with change calculation for cash notes
- Separate menu and raw-material inventory — dishes are billed with quantity only and are not stock tracked
- Raw material stock management with automatic low stock alerts and history, including restock, adjustment and damage logs
- Category and menu management for food, drinks, snacks and desserts
- VAT-compliant tax invoice generation in printable A4 and 80mm thermal receipt formats
- Sales, payment method and profit reporting in NPR
- Role based access for admin, cashier and stock manager accounts
- Responsive light themed interface that works on desktop and mobile browsers
- Live operational dashboard with sales trends and payment distribution charts

## Tech Stack

| Layer       | Technology                              |
| ----------- | --------------------------------------- |
| Backend     | PHP 8.2, Laravel 12                     |
| Frontend    | Blade templates, Tailwind CSS, Lucide   |
| Database    | MySQL or MariaDB                        |
| Server      | PHP built-in server or XAMPP Apache     |
| Tooling     | Composer, Git, GitHub Actions (optional)| 

## Getting Started

### Prerequisites

- PHP 8.2 or newer
- MySQL or MariaDB (XAMPP recommended)
- Composer 2.x (only required when reinstalling dependencies)

### Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/ankitkhatrik6/KhajaPOS.git
   cd KhajaPOS
   ```

2. Install PHP dependencies (optional because the vendor directory is committed):

   ```bash
   composer install
   ```

3. Prepare the environment file and generate an application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure the database credentials inside the `.env` file to match your MySQL or MariaDB server.

5. Create the database, run migrations and seed demo data:

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. Start the development server:

   ```bash
   php artisan serve --host=0.0.0.0 --port=3000
   ```

7. Open the application and sign in:

   ```text
   http://localhost:3000
   ```

## Default Accounts

The database seeder creates a single administrator account (roles for Cashier and Stock Manager can be assigned to additional staff from Settings → Staff & Roles):

| Role  | Email              | Password      | Access             |
| ----- | ------------------ | ------------- | ------------------ |
| Admin | admin@khajapos.com | KhajaPOS@123  | Full system access |

## Payments

KhajaPOS supports two payment methods on the POS terminal:

- Cash, with quick note presets and automatic change calculation
- Online, with an optional transaction reference field

## Project Structure

```text
app/            Models, services, controllers and middleware
config/         Application configuration files
routes/         HTTP routes
resources/      Blade views and static assets
public/         Public files including images and the entry point
database/       Migrations and seeders
tests/          Unit and feature tests
```

## Contributing

Contributions are welcome. Please open an issue or submit a pull request with a clear description of the change.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details. Copyright (c) 2026 Ankit Khatri KC.
