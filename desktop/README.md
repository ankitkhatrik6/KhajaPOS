<div align="center">

<img src="../public/logo.png" alt="KhajaPOS logo" width="120">

# KhajaPOS — Desktop App (Linux)

### Restaurant Point of Sale, Inventory and Billing System

[![Debian](https://img.shields.io/badge/Package-.deb-2ea043)](khajapos_2.2.0-1_all.deb)
[![Version](https://img.shields.io/badge/Version-2.2.0-7c3aed)](khajapos_2.2.0-1_all.deb)
[![License](https://img.shields.io/badge/License-MIT-brightgreen)](../LICENSE)

</div>

The **KhajaPOS Desktop App** is the Linux (Debian / Ubuntu) version of the same
system that runs in the browser. It installs the complete application as a
**local server** on your machine and shows the whole POS — billing terminal,
inventory, reports and settings — inside a **native desktop window**. No browser
is required, and the server starts automatically with your computer.

- One self-contained installation: application + database + dependencies.
- Runs 100% on your machine — no internet connection needed while selling.
- Also reachable from other devices on your local network
  (`http://<this-machine-ip>:3000`).

> Looking for the browser version instead?
> See the [main README](../README.md#web-browser-version).

---

## 1. Download

Download the installer from the table below and save it anywhere
(e.g. `~/Downloads/`).

| File | Size | Description |
| ---- | ---- | ----------- |
| [`khajapos_2.2.0-1_all.deb`](khajapos_2.2.0-1_all.deb) | ~30 MB | Debian / Ubuntu package (64-bit, all architectures) |

**Latest release:** download the newest build from the
[KhajaPOS GitHub Releases page](https://github.com/ankitkhatrik6/KhajaPOS/releases)
(`khajapos_2.2.0-1_all.deb` or newer).

### Checksum (optional)

```bash
sha256sum ~/Downloads/khajapos_2.2.0-1_all.deb
```

## 2. System requirements

- **Distro:** Debian 11+ or Ubuntu 20.04+ (any other `.deb` based distro works too)
- **Architecture:** 64-bit (`amd64`) or ARM64
- **Disk space:** ~500 MB free (package + MySQL data)
- **RAM:** 2 GB recommended
- Nothing needs to be pre-installed — PHP, MariaDB/MySQL and the GTK desktop
  stack are installed automatically from the apt repositories.

## 3. Install

Open a terminal in the folder where you downloaded the file and run:

```bash
cd ~/Downloads
sudo apt install ./khajapos_2.2.0-1_all.deb
```

What happens during installation (first time):

1. apt downloads PHP CLI, the PHP MySQL driver, MariaDB/MySQL server and the
   GTK + WebKit2GTK desktop stack.
2. A dedicated `restaurant_pos` database and `pos_user` account are created.
3. The Laravel migrations run and the demo data is seeded **only when the
   database is empty** (your data is never overwritten on upgrades).
4. A `khajapos` systemd service is enabled, so the POS starts at every boot.
5. The "KhajaPOS" entry is added to the application menu.

When it finishes you will see the admin login and access URL on screen.

## 4. Launch the app

**From the application menu**

Click the **KhajaPOS** icon. A window opens, the local server starts
automatically (a one-time password prompt may appear) and the point of sale
loads inside the app window.

**From the terminal**

```bash
khajapos app           # open the desktop window
khajapos app --kiosk   # fullscreen (ideal for a POS counter)
khajapos open          # start the server and open the browser instead
```

**Keyboard shortcuts inside the app window**

| Key     | Action                      |
| ------- | --------------------------- |
| `Ctrl+P` | Print the invoice / receipt (system print dialog) |
| `Ctrl+R` | Reload the POS            |
| `Ctrl+B` | Open in the web browser   |
| `F11`    | Toggle fullscreen         |
| `Ctrl+Q` | Quit                      |

> **Printing:** select the 80 mm thermal receipt option (or the A4 invoice)
> in the POS, then press `Ctrl+P` / **File → Print** and pick your printer.

## 5. Sign in

| Field    | Value                 |
| -------- | --------------------- |
| Email    | `admin@khajapos.com`  |
| Password | `KhajaPOS@123`        |

> Change the admin password and add staff accounts from **Settings → Staff & Roles**.

## 6. Using it from the browser instead

The same installation also serves the POS as a normal website:

- Open `http://localhost:3000` in any browser on this machine, or
- Open `http://<this-machine-ip>:3000` from any phone/tablet/PC on the same
  network (allow TCP port 3000 in the firewall if needed).

## 7. Management commands

```text
khajapos app [--kiosk]        open the desktop app window
khajapos open                 start the server and open the browser
khajapos status               show service status
khajapos start|stop|restart   control the application service (root)
khajapos enable|disable       start on boot / stop starting on boot (root)
khajapos url                  show the access URL(s) and port
khajapos config               edit /etc/khajapos/.env (root)
khajapos logs                 follow the application log
khajapos backup               dump the database to /var/lib/khajapos/backups (root)
khajapos restore FILE         restore a database backup (root)
khajapos doctor               diagnose PHP, database and service (root)
```

Example:

```bash
khajapos status
sudo khajapos doctor
sudo khajapos backup
```

## 8. Configuration

```bash
sudo khajapos config
```

Key settings in `/etc/khajapos/.env`:

| Setting     | Purpose                                        |
| ----------- | ---------------------------------------------- |
| `APP_URL`   | URL other machines use to reach this server    |
| `APP_DEBUG` | `false` hides error details in production      |
| `DB_DATABASE`| Database name (default: `restaurant_pos`)     |
| `DB_PASSWORD`| Password of the `pos_user` database account   |

The service restarts automatically after you save. `APP_KEY` and `DB_PASSWORD`
are generated on first install — keep a backup with `sudo khajapos backup`.

## 9. Upgrade

Download the newer `.deb` and install it over the current one — your data and
configuration are kept:

```bash
sudo apt install ./khajapos_<new-version>_all.deb
```

## 10. Uninstall

```bash
# keep everything (configuration, data, database)
sudo apt remove khajapos

# remove everything, including configuration and the database
sudo apt purge khajapos
```

## 11. Troubleshooting

Run `sudo khajapos doctor` — it checks PHP, the MySQL driver, the database,
the service and the web server:

```text
PHP CLI             OK (PHP 8.x)
MySQL/Pdo driver    OK
MariaDB/MySQL server OK (database 'restaurant_pos' reachable)
Service (systemd)   RUNNING
HTTP on :3000       OK (web server responding)
```

Common fixes:

- **MySQL port 3306 already used (e.g. XAMPP):**
  stop the other server first, then retry:
  ```bash
  sudo /opt/lampp/lampp stopmysql
  sudo apt --fix-broken install
  sudo dpkg --configure -a
  ```
- **Service not running:** `sudo khajapos restart`, then `sudo khajapos logs`
- **Other devices cannot connect:** allow TCP port 3000 in the firewall and
  check that `APP_URL` points to this machine's LAN IP (`sudo khajapos config`).

## 12. What gets installed

```
/opt/khajapos/               Application code (Laravel 12 + vendor/)
/etc/khajapos/.env           Main configuration file (dpkg conffile)
/usr/lib/systemd/system/khajapos.service   systemd unit (auto-start at boot)
/usr/bin/khajapos            Control command (app, open, status, ...)
/usr/bin/khajapos-app        Native desktop window (GTK + WebKit2GTK)
/usr/share/applications/khajapos.desktop   App menu entry
/usr/share/icons/hicolor/512x512/apps/khajapos.png
/usr/share/pixmaps/khajapos.png
/var/lib/khajapos/backups/   Database backup directory
/var/log/khajapos/           Application and setup logs
/etc/logrotate.d/khajapos    Log rotation
```

## 13. Build the package from source

The package is rebuilt from this repository with a single script — see
[BUILD.md](BUILD.md) for the full build notes and sanity checks.

```bash
./desktop/build-deb.sh
```

## License

MIT — see [../LICENSE](../LICENSE). Copyright (c) 2026 Ankit Khatri KC.