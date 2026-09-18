# Changelog

All notable changes to KhajaPOS are documented here. The project ships as two
separate versions: the **Web (Browser)** version (this repository root) and the
**Desktop App (Linux)** version (the `desktop/` folder).

## [2.2.2] — 2026-09-18

### Fixed
- **Printing no longer crashes the desktop app.** Clicking *A4 Print* or
  *80 mm Receipt* (or pressing `Ctrl+P`) closed the whole application on
  modern WebKitGTK: the app used WebKit's synchronous `PrintOperation
  .run_dialog()`, which spins a nested GTK main loop while WebKit's `print`
  signal is still being emitted — that re-entry segfaults the app before any
  print dialog could appear. Printing now uses WebKit's asynchronous
  `print_()` API, which shows the very same system print dialog without any
  nested loop, and print errors are logged instead of being fatal.
- The `print` signal handler now reuses the print operation WebKit provides
  (instead of creating a second one) and the `failed` signal is handled, so
  a printer error (offline, out of paper) shows in the log and keeps the
  app running.

### Changed
- Desktop package bumped to `2.2.2` (`desktop/khajapos_2.2.2-1_all.deb`).

## [2.2.1] — 2026-09-18

### Fixed
- **Package upgrade:** the v2.2.0 hot-fix shipped with the same package version
  (`2.2.0-1`) as the original release, so `sudo apt install ./khajapos_2.2.0-1_all.deb`
  reported *"khajapos is already the newest version"* and never replaced the
  installed files. The package is now versioned **`2.2.1-1`** so `apt` performs a
  real upgrade and the fixed app (scrollable About dialog, working 80 mm/A4
  printing) is actually installed.
- If you installed `2.2.0-1` from the release, install `2.2.1-1` — no uninstall
  or data loss involved; your database and `/etc/khajapos/.env` are kept.

### Changed
- Desktop package bumped to `2.2.1` (`desktop/khajapos_2.2.1-1_all.deb`); About
  dialog and docs now report version 2.2.1.

## [2.2.0] — 2026-09-18
## [2.2.0] — 2026-09-18

### Added
- **Logo loading screen:** the desktop app now shows the KhajaPOS logo with a
  gentle pulsing effect (plus spinner and status text) while the local server
  boots.
- **Full About dialog:** Help → About KhajaPOS now describes the whole
  Restaurant Management System — features, role-based access, printing
  (A4 + 80 mm thermal), plus technical details (server URL, systemd service,
  database, config path, default login) and the MIT license.
- **Printer support:** File → Print / `Ctrl+P` prints the current page
  (invoice or 80 mm receipt) through the system print dialog, so any receipt
  or office printer connected to the terminal can be used.
- **Screenshot gallery:** the README now shows the full POS, dashboard, menu,
  inventory, invoices, orders, settings and staff screens.

### Changed
- Desktop package bumped to `2.2.0` (`desktop/khajapos_2.2.0-1_all.deb`).

## [2.1.0] — 2026-09-17

### Added
- **Web / App separation:** the project now clearly splits into two versions —
  the browser version (repository root, `./run-web.sh`) and the native desktop
  app for Linux (`desktop/`, installable `.deb` package).
- **Linux download:** ready-to-install Debian/Ubuntu package
  `desktop/khajapos_2.1.0-1_all.deb` plus a full download & setup guide in
  `desktop/README.md` (requirements, install, launch, upgrade, uninstall,
  troubleshooting).
- **One-command launchers:** `run-web.sh` (starts XAMPP MySQL + web server)
  and `stop-web.sh`.
- **Reproducible package builder:** `desktop/build-deb.sh` rebuilds the `.deb`
  from the current source tree; documented in `desktop/BUILD.md`.
- **CI release workflow:** GitHub Actions builds the Linux `.deb` automatically
  for every `v*` tag and attaches it to the GitHub Release (`v2.1.0`).

### Changed
- Cleaned the repository: removed outdated `.deb` packages (1.0.0, 1.1.0),
  python bytecode caches, the duplicated vendored app copy inside the package
  tree and the personal `runcmds.txt` scratch file.
- README restructured: Web (Browser) and Desktop App (Linux) quick starts,
  downloads table and badges.
- npm scripts aligned with the new launchers (`web`, `stop`, `serve`, `deb`).

## [2.0.0] — 2026-09-17

- Native desktop app window (GTK + WebKit2GTK) with fullscreen POS counter,
  zoom, reload and about dialog.
- Local-server package: systemd auto-start, MariaDB/MySQL provisioning,
  migrations and demo seeding on first install, `khajapos` control command
  (status/start/stop/config/backup/doctor).
- Role based access and admin-only permanent deletion (staff, invoices,
  inventory, categories).

## [1.0.0] — initial release

- Browser-based Restaurant POS, inventory and billing on Laravel 12 + MySQL.