#!/usr/bin/env bash
# ============================================================================
# KhajaPOS - Web (Browser) version stopper
# ----------------------------------------------------------------------------
# Stops the Laravel web server and, when XAMPP is installed, the MySQL server.
# ============================================================================
set -e

echo "[khajapos] Stopping the KhajaPOS web server ..."
pkill -f 'artisan serve' >/dev/null 2>&1 || true

if [ -d /opt/lampp ]; then
    echo "[khajapos] Stopping MySQL (XAMPP) ..."
    sudo /opt/lampp/lampp stopmysql
fi

echo "[khajapos] Web version stopped."