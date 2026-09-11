# Malibhu View Resort

Resort reservation system built with PHP and SQLite. Guests can register, book event
reservations, and view receipts. Admins review and approve or reject bookings.

## Structure

```
admin/      Admin dashboard, login, approve/reject actions
user/       Registration, login, reservations, receipts
config/     Database connection and admin authentication
includes/   Shared header and footer
css/ js/    Front-end assets
images/     Logo and gallery images
database.sql  Schema
```

## Setup

Requires PHP 7.0+ with the PDO SQLite extension.

```bash
php -S localhost:8000
```

The SQLite database file (`database.db`) is created automatically on first request by
`config/db.php`, which also applies the schema. It is not tracked in git.

Open http://localhost:8000 in a browser.
