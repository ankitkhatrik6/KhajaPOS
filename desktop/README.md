<div align="center">

<img src="../public/logo.png" alt="KhajaPOS logo" width="120">

# KhajaPOS — Desktop App (Linux)

### Restaurant Point of Sale, Inventory and Billing System

[![Debian](https://img.shields.io/badge/Package-.deb-2ea043?style=flat-square&logo=debian&logoColor=white)](khajapos_2.2.3-1_all.deb)
[![Version](https://img.shields.io/badge/Version-2.2.3-7c3aed?style=flat-square)](khajapos_2.2.3-1_all.deb)
[![PHP](https://img.shields.io/badge/PHP-8.2-166534?style=flat-square&logo=php&logoColor=white)](../composer.json)
[![Laravel](https://img.shields.io/badge/Laravel-12-ff2d20?style=flat-square&logo=laravel&logoColor=white)](../composer.json)
[![MariaDB](https://img.shields.io/badge/MariaDB-11.8-003545?style=flat-square&logo=mariadb&logoColor=white)](https://mariadb.org/)
[![MySQL](https://img.shields.io/badge/MySQL-Compatible-4479a1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![GTK](https://img.shields.io/badge/GTK-3-4a86cf?style=flat-square&logo=gtk&logoColor=white)](https://www.gtk.org/)
[![WebKitGTK](https://img.shields.io/badge/WebKitGTK-4.1-7a7a7a?style=flat-square)](https://webkitgtk.org/)
[![License](https://img.shields.io/badge/License-MIT-22c55e?style=flat-square)](../LICENSE)

</div>

The **KhajaPOS Desktop App** is the Linux (Debian / Ubuntu) version of the same
restaurant management system that runs in the browser.

It installs the complete Laravel application as a **local server** on your
computer and displays the POS inside a **native GTK desktop window**.

No external browser is required.

The application includes:

- POS billing terminal
- Menu management
- Inventory and stock management
- Staff and role management
- Reports
- Settings
- Invoice and receipt printing
- Local database
- LAN access
- Automatic server startup

> Looking for the browser version instead?
> See the [main README](../README.md#web-browser-version).

---

# 1. System Requirements

### Operating System

Supported systems:

- Debian 11+
- Ubuntu 20.04+
- Ubuntu 22.04+
- Ubuntu 24.04+
- Other compatible Debian-based distributions

### Architecture

- `amd64` / x86_64
- ARM64 where the required dependencies are available

### Hardware

Recommended:

- 2 GB RAM or more
- 500 MB+ free disk space
- Working local network if LAN access is required

### Internet

Internet access is required during the first installation because `apt`
may need to download PHP, MariaDB/MySQL and GTK/WebKit dependencies.

After installation, the POS itself can run locally without an internet
connection.

---

# 2. Download

Download the latest `.deb` installer from the
[KhajaPOS GitHub Releases page](https://github.com/ankitkhatrik6/KhajaPOS/releases).

For version **2.2.3**:

```text
khajapos_2.2.3-1_all.deb
```

Save the package in:

```text
~/Downloads/
```

Verify that it exists:

```bash
ls -lh ~/Downloads/khajapos_2.2.3-1_all.deb
```

Optional checksum verification:

```bash
sha256sum ~/Downloads/khajapos_2.2.3-1_all.deb
```

---

# 3. Before Installation

KhajaPOS installs and uses **MariaDB/MySQL on port 3306**.

If you already have another MySQL/MariaDB server running, especially
XAMPP, stop it before installing KhajaPOS.

Check port 3306:

```bash
sudo ss -ltnp | grep :3306
```

If XAMPP is using MySQL:

```bash
sudo /opt/lampp/lampp stopmysql
```

You can verify again:

```bash
sudo ss -ltnp | grep :3306
```

---

# 4. Install KhajaPOS

Open a terminal:

```bash
cd ~/Downloads
```

Install the package:

```bash
sudo apt install ./khajapos_2.2.3-1_all.deb
```

Using `apt` is recommended because it automatically resolves required
dependencies.

During installation, KhajaPOS installs/configures the following:

1. PHP CLI
2. Required PHP extensions
3. MariaDB/MySQL
4. PHP MySQL/PDO driver
5. GTK 3
6. WebKit2GTK
7. Laravel application
8. KhajaPOS database
9. Database user
10. Laravel migrations
11. Initial seed data
12. Systemd service
13. Desktop application launcher
14. Application menu entry
15. Application icon
16. Backup directory
17. Application log directory

---

# 5. Installed Application Location

After installation, the Laravel application is located at:

```text
/opt/khajapos/
```

The main Laravel executable is:

```text
/opt/khajapos/artisan
```

Configuration:

```text
/etc/khajapos/.env
```

Systemd service:

```text
/usr/lib/systemd/system/khajapos.service
```

Desktop launcher:

```text
/usr/bin/khajapos-app
```

Management command:

```text
/usr/bin/khajapos
```

---

# 6. Database Setup

KhajaPOS automatically creates its database during first installation.

Default database:

```text
Database: restaurant_pos
Host:     127.0.0.1
Port:     3306
User:     pos_user
```

The configuration is stored in:

```text
/etc/khajapos/.env
```

You normally do not need to manually create the database.

To verify that MariaDB is running:

```bash
sudo systemctl status mariadb
```

You should see:

```text
Active: active (running)
```

---

# 7. Access the Database Manually

To open the KhajaPOS database:

```bash
sudo mariadb restaurant_pos
```

List all tables:

```sql
SHOW TABLES;
```

Check users:

```sql
SELECT id, name, email, role_id, is_active FROM users;
```

Check roles:

```sql
SELECT id, name, slug FROM roles;
```

Exit MariaDB:

```sql
exit;
```

---

# 8. Laravel Database Migrations

KhajaPOS automatically runs migrations during initial installation.

To check migration status manually:

```bash
cd /opt/khajapos
sudo -u khajapos php artisan migrate:status
```

A healthy database should show migrations as:

```text
Ran
```

If a future upgrade contains pending migrations:

```bash
cd /opt/khajapos
sudo -u khajapos php artisan migrate --force
```

### Important

Never use this on a database containing real restaurant data:

```bash
php artisan migrate:fresh
```

`migrate:fresh` drops existing tables and can permanently remove
restaurant data.

---

# 9. Initial Database Seed

On a **new empty database**, KhajaPOS seeds the initial roles and
administrator account.

The initial roles are:

```text
Admin
Cashier
Stock Manager
```

Default administrator:

```text
Email:    admin@khajapos.com
Password: KhajaPOS@123
```

After your first login, immediately change the administrator password from:

```text
Settings → Staff & Roles
```

### Important

Do not repeatedly run:

```bash
php artisan db:seed
```

on an existing production database unless the project's seeders are
specifically designed to be safely re-run.

The current seeders create initial records such as roles, so running
them again can cause duplicate-entry errors.

---

# 10. Verify the Installation

KhajaPOS includes a built-in diagnostic command.

Run:

```bash
sudo khajapos doctor
```

A healthy installation should show checks similar to:

```text
PHP CLI             OK
MySQL/Pdo driver    OK
MariaDB/MySQL       OK
Service (systemd)   RUNNING
HTTP on :3000       OK
```

You can also check the service directly:

```bash
sudo systemctl status khajapos.service
```

Check the local Laravel server:

```bash
curl -I http://127.0.0.1:3000
```

Expected:

```text
HTTP/1.1 200 OK
```

---

# 11. Start / Stop the Server

Start:

```bash
sudo khajapos start
```

Stop:

```bash
sudo khajapos stop
```

Restart:

```bash
sudo khajapos restart
```

Check:

```bash
khajapos status
```

Enable automatic startup:

```bash
sudo khajapos enable
```

Disable automatic startup:

```bash
sudo khajapos disable
```

---

# 12. Launch the Desktop App

### Application Menu

Open your desktop application menu and select:

```text
KhajaPOS
```

### Terminal

```bash
khajapos app
```

Kiosk mode:

```bash
khajapos app --kiosk
```

Kiosk mode is useful for a dedicated restaurant POS counter.

---

# 13. Browser Mode

The same Laravel server can also be opened in a normal browser.

Run:

```bash
khajapos open
```

Or manually visit:

```text
http://localhost:3000
```

You can also use:

```text
http://127.0.0.1:3000
```

The desktop application and browser version use the same Laravel
application and database.

---

# 14. LAN Access

KhajaPOS can also be accessed from other devices on the same local
network.

Find the server computer's IP:

```bash
hostname -I
```

For example:

```text
192.168.1.10
```

From another computer, phone or tablet on the same network:

```text
http://192.168.1.10:3000
```

Replace the IP address with the actual IP of your KhajaPOS server.

If another device cannot connect, check your firewall and allow TCP
port `3000`.

For UFW:

```bash
sudo ufw allow 3000/tcp
```

Check:

```bash
sudo ufw status
```

---

# 15. First Login

Open KhajaPOS:

```bash
khajapos app
```

Login with:

```text
Email:
admin@khajapos.com

Password:
KhajaPOS@123
```

After logging in:

1. Change the administrator password.
2. Open **Settings → Staff & Roles**.
3. Create individual accounts for staff.
4. Assign the appropriate role.
5. Configure restaurant/menu settings.
6. Add your actual menu and inventory.
7. Configure your printer if required.

---

# 16. Staff Roles

KhajaPOS includes three default roles.

### Admin

Full access to:

- POS
- Inventory
- Menu
- Staff
- Reports
- Settings
- System administration

### Cashier

Access to:

- POS
- Billing
- Orders
- Receipts

### Stock Manager

Access to:

- Inventory
- Restocking
- Damaged stock
- Stock audits
- Stock management

---

# 17. Printing

KhajaPOS supports normal system printing.

Inside the desktop application:

```text
Ctrl + P
```

For thermal receipts:

```text
80 mm
```

For standard invoices:

```text
A4
```

Select the appropriate printer from the system print dialog.

---

# 18. Management Commands

```text
khajapos app [--kiosk]
```

Open the native desktop application.

```text
khajapos open
```

Start the server and open the browser.

```text
khajapos status
```

Show service status.

```text
sudo khajapos start
```

Start the service.

```text
sudo khajapos stop
```

Stop the service.

```text
sudo khajapos restart
```

Restart the service.

```text
sudo khajapos enable
```

Enable startup at boot.

```text
sudo khajapos disable
```

Disable startup at boot.

```text
khajapos url
```

Show access URLs.

```text
sudo khajapos config
```

Edit KhajaPOS configuration.

```text
khajapos logs
```

Follow KhajaPOS logs.

```text
sudo khajapos backup
```

Create a database backup.

```text
sudo khajapos restore FILE
```

Restore a database backup.

```text
sudo khajapos doctor
```

Run system diagnostics.

---

# 19. Configuration

Open the configuration editor:

```bash
sudo khajapos config
```

Configuration file:

```text
/etc/khajapos/.env
```

Important settings:

| Setting | Description |
| --- | --- |
| `APP_URL` | Application URL |
| `APP_DEBUG` | Laravel debug mode |
| `DB_HOST` | Database host |
| `DB_PORT` | Database port |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database username |
| `DB_PASSWORD` | Database password |

For normal restaurant use:

```text
APP_DEBUG=false
```

Do not publish `.env` or its credentials to GitHub.

---

# 20. Database Backup

Before making major changes or upgrading, create a backup:

```bash
sudo khajapos backup
```

Backups are stored in:

```text
/var/lib/khajapos/backups/
```

You can also create a direct MariaDB backup:

```bash
sudo mariadb-dump restaurant_pos > ~/khajapos-backup.sql
```

Verify:

```bash
ls -lh ~/khajapos-backup.sql
```

---

# 21. Restore Database

Using the KhajaPOS management command:

```bash
sudo khajapos restore /path/to/backup.sql
```

Or restore a manual MariaDB dump:

```bash
sudo mariadb restaurant_pos < ~/khajapos-backup.sql
```

Always make a fresh backup before performing a restore.

---

# 22. Upgrade to a New Version

KhajaPOS supports installing newer `.deb` packages over an existing
installation.

Your existing:

- Database
- Restaurant data
- Users
- Menu
- Inventory
- Configuration
- `.env`

should be preserved by the package upgrade process.

### Step 1 — Create a backup

```bash
sudo khajapos backup
```

### Step 2 — Download the new release

Download the newest `.deb` from:

[KhajaPOS Releases](https://github.com/ankitkhatrik6/KhajaPOS/releases)

Example:

```text
khajapos_2.2.3-1_all.deb
```

### Step 3 — Install over the existing version

```bash
cd ~/Downloads
sudo apt install ./khajapos_2.2.3-1_all.deb
```

Do **not** uninstall the old version first.

Do **not** use:

```bash
sudo apt purge khajapos
```

for a normal upgrade.

### Step 4 — Check the service

```bash
sudo systemctl status khajapos.service
```

### Step 5 — Run diagnostics

```bash
sudo khajapos doctor
```

### Step 6 — Check migrations

```bash
cd /opt/khajapos
sudo -u khajapos php artisan migrate:status
```

If the new release contains pending migrations:

```bash
sudo -u khajapos php artisan migrate --force
```

### Important

Never run:

```bash
php artisan migrate:fresh
```

during an upgrade.

---

# 23. Troubleshooting

## `khajapos-app: Permission denied`

Run:

```bash
sudo chmod +x /usr/bin/khajapos-app
```

Then:

```bash
khajapos app
```

If required:

```bash
sudo chown root:root /usr/bin/khajapos-app
sudo chmod 755 /usr/bin/khajapos-app
```

---

## Service is not running

Run:

```bash
sudo khajapos restart
```

Then:

```bash
sudo khajapos status
```

View logs:

```bash
sudo khajapos logs
```

or:

```bash
sudo journalctl -u khajapos.service -n 100 --no-pager
```

---

## Database is not available

Check MariaDB:

```bash
sudo systemctl status mariadb
```

Check the database:

```bash
sudo mariadb restaurant_pos
```

Then run:

```bash
sudo khajapos doctor
```

---

## Port 3306 conflict

Check:

```bash
sudo ss -ltnp | grep :3306
```

If XAMPP is using MySQL:

```bash
sudo /opt/lampp/lampp stopmysql
```

Then:

```bash
sudo systemctl restart mariadb
```

---

## Port 3000 conflict

Check:

```bash
sudo ss -ltnp | grep :3000
```

Then:

```bash
sudo systemctl status khajapos.service
```

---

## POS is not loading

First check:

```bash
sudo khajapos doctor
```

Then:

```bash
curl -I http://127.0.0.1:3000
```

If the server returns:

```text
HTTP/1.1 200 OK
```

the Laravel application is responding.

You can also test GTK/WebKit directly:

```bash
python3 -c "import gi; gi.require_version('Gtk','3.0'); gi.require_version('WebKit2','4.1'); from gi.repository import Gtk, WebKit2; w=Gtk.Window(); w.set_default_size(1000,700); v=WebKit2.WebView(); v.load_uri('http://127.0.0.1:3000'); w.add(v); w.connect('destroy',Gtk.main_quit); w.show_all(); Gtk.main()"
```

---

## `Could not open input file: artisan`

Make sure you are inside the Laravel application directory:

```bash
cd /opt/khajapos
```

Then run:

```bash
sudo -u khajapos php artisan <command>
```

For example:

```bash
sudo -u khajapos php artisan migrate:status
```

---

# 24. XAMPP Compatibility

KhajaPOS uses the system MariaDB/MySQL service.

If XAMPP is already running its own MySQL server, the two database servers
may attempt to use port `3306`.

Stop XAMPP MySQL before using KhajaPOS:

```bash
sudo /opt/lampp/lampp stopmysql
```

Then:

```bash
sudo systemctl restart mariadb
```

Verify:

```bash
sudo systemctl status mariadb
```

---

# 25. Desktop Application Shortcuts

| Shortcut | Action |
| --- | --- |
| `Ctrl + P` | Print invoice/receipt |
| `Ctrl + R` | Reload POS |
| `Ctrl + B` | Open in browser |
| `F11` | Toggle fullscreen |
| `Ctrl + Q` | Quit application |

---

# 26. Installed Files

```text
/opt/khajapos/
```

Laravel application.

```text
/etc/khajapos/.env
```

Application configuration.

```text
/usr/lib/systemd/system/khajapos.service
```

Systemd service.

```text
/usr/bin/khajapos
```

KhajaPOS management command.

```text
/usr/bin/khajapos-app
```

Native GTK/WebKit desktop application.

```text
/usr/share/applications/khajapos.desktop
```

Desktop application entry.

```text
/usr/share/icons/hicolor/512x512/apps/khajapos.png
```

Application icon.

```text
/var/lib/khajapos/backups/
```

Database backups.

```text
/var/log/khajapos/
```

Application and setup logs.

```text
/etc/logrotate.d/khajapos
```

Log rotation configuration.

---

# 27. Build the Package From Source

Clone the repository:

```bash
git clone https://github.com/ankitkhatrik6/KhajaPOS.git
cd KhajaPOS
```

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Create the environment file:

```bash
cp .env.example .env
```

Generate the Laravel application key:

```bash
php artisan key:generate
```

Configure your database in `.env`.

Run migrations:

```bash
php artisan migrate
```

Seed a fresh development database:

```bash
php artisan db:seed
```

Build the Debian package:

```bash
./desktop/build-deb.sh
```

See [BUILD.md](BUILD.md) for the complete packaging and build process.

---

# 28. Architecture

```text
                         KhajaPOS Desktop
                                │
                                ▼
                     ┌─────────────────────┐
                     │     GTK 3 Window    │
                     │     WebKit2GTK       │
                     └──────────┬──────────┘
                                │
                                ▼
                     http://127.0.0.1:3000
                                │
                                ▼
                     ┌─────────────────────┐
                     │     Laravel 12      │
                     │   KhajaPOS Backend  │
                     └──────────┬──────────┘
                                │
                                ▼
                     ┌─────────────────────┐
                     │    MariaDB/MySQL    │
                     │    restaurant_pos   │
                     └─────────────────────┘
```

The same Laravel application can be accessed through:

```text
Native Desktop App
        │
        ├── localhost:3000
        │
        └── LAN devices
```

---

# 29. Project Structure

```text
KhajaPOS/
├── app/
│   ├── Http/
│   ├── Models/
│   └── ...
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   └── views/
├── routes/
├── desktop/
│   ├── build-deb.sh
│   └── ...
├── composer.json
├── package.json
├── artisan
├── BUILD.md
└── README.md
```

---

# 30. Security Recommendations

For a real restaurant installation:

- Change the default administrator password immediately.
- Create separate accounts for staff.
- Never share the administrator account.
- Assign only the required role to each staff member.
- Back up the database regularly.
- Keep `/etc/khajapos/.env` private.
- Keep `APP_DEBUG=false` for normal deployments.
- Do not expose port `3000` directly to the public internet.
- Restrict LAN access when appropriate.
- Keep the operating system and packages updated.

---

# 31. Quick Start

For an experienced user, the complete installation is:

```bash
cd ~/Downloads
sudo apt install ./khajapos_2.2.3-1_all.deb
```

Verify:

```bash
sudo khajapos doctor
```

Launch:

```bash
khajapos app
```

Login:

```text
Email:    admin@khajapos.com
Password: KhajaPOS@123
```

For a new installation, the database, migrations, roles and initial
administrator are configured automatically.

---

# License

MIT — see [../LICENSE](../LICENSE).

Copyright (c) 2026 Ankit Khatri KC.

---

<div align="center">

### KhajaPOS

**Restaurant billing, inventory and operations — running locally on Linux.**

[GitHub](https://github.com/ankitkhatrik6/KhajaPOS) ·
[Releases](https://github.com/ankitkhatrik6/KhajaPOS/releases)

</div>
