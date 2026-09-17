#!/usr/bin/env bash
# ============================================================================
# KhajaPOS - Debian package builder for the Desktop App (Linux) version
# ----------------------------------------------------------------------------
# Rebuilds desktop/khajapos_<version>_all.deb from the current source tree.
# The app code, vendor directory and icons are synced into the package tree,
# so the .deb is always reproducible from the repository root.
#
# Usage:
#     ./desktop/build-deb.sh
#
# Output:
#     desktop/khajapos_<VERSION>-1_all.deb
# ============================================================================
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DESKTOP="$ROOT/desktop"
TREE="$DESKTOP/khajapos"
VERSION="2.1.0"

cd "$ROOT"

# 1) Make sure the package's generated app directory exists (it is not stored
#    in git because git does not track empty/ignored directories).
mkdir -p "$TREE/opt/khajapos"

# 2) Sync the current application source into the package tree. The dest is
#    wiped first (--delete) so stale files never end up in a release build.
echo "[khajapos] Syncing application source into the package tree ..."
rsync -a --delete \
    --exclude='.git' --exclude='.gitignore' --exclude='.env' \
    --exclude='.env.backup' --exclude='.phpunit.result.cache' \
    --exclude='.phpunit.cache' --exclude='node_modules' \
    --exclude='demo' --exclude='tests' --exclude='storage' \
    --exclude='desktop' --exclude='packaging' --exclude='.github' \
    --exclude='run-web.sh' --exclude='stop-web.sh' \
    "$ROOT"/ "$TREE/opt/khajapos/"

# 3) The packaged app keeps its own empty env template; postinst fills in the
#    APP_KEY and DB_PASSWORD on the target machine during first install.
cp "$TREE/etc/khajapos/.env" "$TREE/opt/khajapos/.env.package"

# 4) Icons shipped by the package
cp "$ROOT/public/logo.png" "$TREE/usr/share/icons/hicolor/512x512/apps/khajapos.png"
cp "$ROOT/public/logo.png" "$TREE/usr/share/pixmaps/khajapos.png"

# 5) Keep the empty runtime directories in the package
mkdir -p "$TREE/var/lib/khajapos/backups" "$TREE/var/log/khajapos"
touch "$TREE/var/lib/khajapos/backups/.keep" "$TREE/var/log/khajapos/.keep"

# 6) Never ship python bytecode caches, test caches or root-specific launchers
rm -rf "$TREE"/usr/bin/__pycache__ "$TREE"/var/lib/khajapos/backups/__pycache__
rm -f "$TREE/opt/khajapos/run-web.sh" "$TREE/opt/khajapos/stop-web.sh"
rm -f "$TREE/opt/khajapos/.phpunit.result.cache"
rm -rf "$TREE/opt/khajapos/.phpunit.cache"

# 7) Remove the previously built package so a broken build is obvious
rm -f "$DESKTOP"/khajapos_"${VERSION}"-1_all.deb

# 8) Build the package (file ownership is normalised to root)
echo "[khajapos] Building khajapos_${VERSION}-1_all.deb ..."
dpkg-deb --build --root-owner-group "$TREE" "$DESKTOP/khajapos_${VERSION}-1_all.deb"

echo ""
echo "[khajapos] Done: $DESKTOP/khajapos_${VERSION}-1_all.deb"
echo "[khajapos] Install with: sudo apt install ./khajapos_${VERSION}-1_all.deb"