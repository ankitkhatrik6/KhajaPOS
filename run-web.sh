#!/usr/bin/env bash
# ============================================================================
# KhajaPOS - Web (Browser) version launcher
# ----------------------------------------------------------------------------
# Starts the MySQL/MariaDB server and the Laravel web server. The POS is then
# served at http://localhost:3000 - open that address in your browser.
#
# Usage:
#     ./run-web.sh            start MySQL and the web server
#     ./stop-web.sh           stop the web server and MySQL
# ============================================================================
set -e

cd "$(dirname "$0")"

echo "[khajapos] Web version launcher"
echo "--------------------------------"

# 1) Start MySQL (XAMPP)
if [ -d /opt/lampp ]; then
    echo "[khajapos] Starting MySQL (XAMPP) ..."
    sudo /opt/lampp/lampp startmysql
else
    # Try the system service when XAMPP is not installed
    if command -v systemctl >/dev/null 2>&1; then
        systemctl start mariadb 2>/dev/null || systemctl start mysql 2>/dev/null || true
    fi
fi

# 2) Start the Laravel development server
echo "[khajapos] Starting web server on http://localhost:3000"
php artisan serve --host=0.0.0.0 --port=3000