# Changelog

All notable changes to KhajaPOS are documented here. The project ships as two
separate versions: the **Web (Browser)** version (this repository root) and the
**Desktop App (Linux)** version (the `desktop/` folder).

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