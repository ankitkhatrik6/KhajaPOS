KhajaPOS - Debian Package (Native Desktop App)
=============================================

KhajaPOS is a restaurant Point of Sale, inventory and billing system built on
PHP and Laravel. This package installs the COMPLETE system as a LOCAL SERVER
application with a NATIVE DESKTOP WINDOW: the whole interface - billing
terminal, inventory, categories, reports, settings - is shown inside the
KhajaPOS app window itself. No browser is required.

The browser version of the same system is available from the KhajaPOS
repository: https://github.com/ankitkhatrik6/KhajaPOS

Installation
------------

Download the latest installer (khajapos_2.2.4-1_all.deb or newer) from the
GitHub Releases page, then install it:

    https://github.com/ankitkhatrik6/KhajaPOS/releases

    sudo apt install ./khajapos_2.2.4-1_all.deb

The package installs the required dependencies (PHP CLI, PHP MySQL driver,
MariaDB/MySQL server, GTK + WebKit2GTK), creates a dedicated
"restaurant_pos" database with its own user, runs the Laravel migrations,
seeds the initial data, enables the server at boot and registers the app.

Using the desktop app
---------------------

  * Click "KhajaPOS" in the application menu. A window opens, the local
    server starts automatically (a one-time password prompt may appear) and
    the point of sale loads INSIDE the app window.

  * Right-click the "KhajaPOS" menu icon and choose
      - "KhajaPOS (Fullscreen / POS Counter)" for a fullscreen POS terminal,
      - "Open in Web Browser" to use the browser instead.

  * Keyboard shortcuts inside the app window:
      Ctrl+R reload        Ctrl+B open in browser
      Ctrl+Q quit          F11 fullscreen

  * The server auto-starts at every boot (systemd). From the command line:

      khajapos app           open the desktop window
      khajapos app --kiosk   open fullscreen
      khajapos open          open in the default browser

Default administrator login (shown inside the app):

    Email    : admin@khajapos.com
    Password : KhajaPOS@123

What gets installed
-------------------

    /opt/khajapos/               Application code (Laravel 12 + vendor/)
    /etc/khajapos/.env           Main configuration file (dpkg conffile)
    /usr/lib/systemd/system/khajapos.service
                                 systemd service unit (auto-start on boot)
    /usr/bin/khajapos            Control command (app, open, status, ...)
    /usr/bin/khajapos-app        Native desktop window (GTK + WebKit2GTK)
    /usr/share/applications/khajapos.desktop
                                 App menu entry + fullscreen/browser actions
    /usr/share/icons/hicolor/512x512/apps/khajapos.png
    /usr/share/pixmaps/khajapos.png
    /var/lib/khajapos/backups/   Database backup directory
    /var/log/khajapos/           Application and setup logs
    /etc/logrotate.d/khajapos    Log rotation

Management
----------

    khajapos app [--kiosk]        open the desktop app window
    khajapos open                 start the server and open the browser
    khajapos status               show service status
    khajapos start | stop | restart | enable | disable
    khajapos config               edit /etc/khajapos/.env
    khajapos logs                 follow the application log
    khajapos backup               dump the database (with root)
    khajapos restore FILE         restore a database backup (with root)
    khajapos doctor               diagnose PHP, database and service

Configuration (khajapos config)
-------------------------------

Key settings in /etc/khajapos/.env:

    APP_URL       URL other machines use to reach this server (auto-detected)
    APP_DEBUG     set to false to hide error details
    DB_PASSWORD   password of the "pos_user" database account
    DB_DATABASE   database name (default: restaurant_pos)

After changing the configuration the service is restarted automatically.
Both APP_KEY (Laravel encryption key) and DB_PASSWORD are generated during
the first install; back them up with "khajapos backup".

Removal / Upgrade
-----------------

    # keep everything (configuration, data, database)
    sudo apt remove khajapos

    # remove everything, including configuration and the database
    sudo apt purge khajapos

    # upgrade to a newer build
    sudo apt install ./khajapos_<version>_all.deb

Local network access
--------------------

The server listens on 0.0.0.0:3000, so other devices on the same network can
use the POS by opening http://<this-machine-ip>:3000. Check your firewall if
it cannot connect (allow TCP port 3000).

Troubleshooting
---------------

If "khajapos doctor" reports a problem:

  * PHP CLI missing        -> sudo apt install php-cli
  * MySQL driver missing   -> sudo apt install php-mysql
  * Database unreachable   -> sudo systemctl start mariadb
  * Service not running    -> sudo khajapos restart; sudo khajapos logs

If the apt install stopped because another MySQL (for example XAMPP) is
already running on port 3306, stop it first and retry:

    sudo /opt/lampp/lampp stopmysql
    sudo apt --fix-broken install
    sudo dpkg --configure -a

What the package does NOT do
----------------------------

It serves the /opt/khajapos application with Laravel's built-in server
(php artisan serve), which is single threaded and ideal for one cash
counter or a small LAN. For very busy sites front the same application with
Apache 2 or Nginx + PHP FPM instead.

License: MIT. Copyright (c) 2026 Ankit Khatri KC.