#!/usr/bin/env bash
# ============================================================================
# KhajaPOS - Web (Browser) version stopper
# ----------------------------------------------------------------------------
# Stops the Laravel web server that run-web.sh started from this directory
# and, when XAMPP is installed, the MySQL server.
#
# Only `php artisan serve` processes whose working directory is this project
# are stopped, so the packaged KhajaPOS desktop service (/opt/khajapos) and
# any other Laravel project on the machine keep running.
# ============================================================================
set -e

APP_DIR="$(cd "$(dirname "$0")" && pwd)"

echo "[khajapos] Stopping the KhajaPOS web server ..."
stopped=0
for pid in $(pgrep -f 'artisan serve' 2>/dev/null || true); do
    if [ "$(readlink "/proc/$pid/cwd" 2>/dev/null)" = "$APP_DIR" ]; then
        kill "$pid" 2>/dev/null && stopped=$((stopped + 1)) || true
    fi
done

if [ "$stopped" -gt 0 ]; then
    echo "[khajapos] Web server stopped ($stopped process(es))."
else
    echo "[khajapos] No KhajaPOS web server from this directory is running."
fi

if [ -d /opt/lampp ]; then
    echo "[khajapos] Stopping MySQL (XAMPP) ..."
    sudo /opt/lampp/lampp stopmysql
fi

echo "[khajapos] Web version stopped."