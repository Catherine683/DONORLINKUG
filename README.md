# DonorLink UG

DonorLink UG is a blood donation appointment and attendance platform for the
Uganda Blood Transfusion Services (UBTS). It connects donors with published
blood donation drives, manages appointment capacity, supports staff attendance
workflows, and records donation outcomes.

The platform manages appointments and operations only. It does not calculate
donor eligibility or make medical decisions; those remain with qualified UBTS
medical professionals.

## Features

- Public browse-and-search for upcoming blood donation drives
- Donor registration, email verification, profile, and account settings
- Appointment booking, rescheduling, cancellation, and `.ics` calendar exports
- Atomic slot capacity enforcement and concurrent booking protection
- Staff drive, slot, roster, check-in, and walk-in management
- Admin staff invitations, audit logs, notification logs, and system status
- Donation outcome recording and derived donor donation history
- CSV reports and exports with formula-injection protection
- Queued appointment and reminder email notifications
- Brevo SMTP quota protection and transactional email reserves

## Technology

- PHP 8.3+
- Laravel 13
- Livewire 4
- Fortify
- SQLite for local development
- PostgreSQL (Neon target) or MySQL for staging and production
- Vite 8 and Tailwind CSS 4
- Pest 4 for tests

## Requirements

- PHP 8.3 or newer with PDO, cURL, MBString, and OpenSSL extensions
- Composer 2
- Node.js 20 or newer
- npm

## Local setup

From a terminal in the repository root:

```bash
composer install
npm install
```

Create the local environment file:

```bash
# macOS/Linux
cp .env.example .env
```

```powershell
# Windows PowerShell
Copy-Item .env.example .env
```

Create the SQLite database and application key:

```bash
# macOS/Linux
touch database/database.sqlite
```

```powershell
# Windows PowerShell
New-Item -ItemType File database/database.sqlite -Force
```

```bash
php artisan key:generate
php artisan migrate --seed
npm run build
```

For development assets with hot reload, use `npm run dev` in a separate
terminal. Start the application in another terminal:

```bash
php artisan serve
```

Open `http://localhost:8000`.

The seeder creates local-only users with password `password`:

| Role | Email |
| --- | --- |
| Administrator | `admin@donorlink.test` |
| Staff | `staff@donorlink.test` |

Do not run `migrate:fresh`, `migrate:reset`, or seeders against production data.

## Local email and background jobs

For local development, set this in `.env`:

```env
MAIL_MAILER=log
QUEUE_CONNECTION=database
```

Outgoing email is written to `storage/logs/laravel.log`. Process queued mail in
a separate terminal:

```bash
php artisan queue:work
```

Run scheduled reminder and announcement sweeps locally:

```bash
php artisan schedule:work
```

To simulate an appointment reminder sweep at a chosen time:

```bash
php artisan donorlink:send-reminders --now="2026-10-24 09:00"
```

The `--now` option is disabled in production.

## Testing and quality checks

The test suite uses SQLite in memory and array mail/queue drivers:

```bash
composer check
```

This runs Pint in check mode and the Pest suite. You can run the suite directly:

```bash
php artisan test
```

To run tests against the dedicated MySQL configuration:

```bash
php artisan test --configuration=phpunit.mysql.xml
```

Useful development commands:

```bash
php artisan donorlink:booking-race-check
php artisan donorlink:preflight
php artisan donorlink:test-email
```

## Scheduled operations

The Laravel scheduler runs:

| Command | Frequency |
| --- | --- |
| `donorlink:heartbeat` | Every minute |
| Queue worker heartbeat | Every five minutes |
| `donorlink:send-reminders` | Every ten minutes |
| `donorlink:dispatch-announcements` | Every ten minutes |
| `queue:prune-failed --hours=168` | Daily |

Production requires both the Laravel scheduler and a database queue worker.
The deployment configuration in this repository targets Render with Neon
PostgreSQL:

```bash
php artisan schedule:work
php artisan queue:work database --sleep=3 --tries=3 --timeout=90
```

For hosted deployments, configure scheduler and worker processes in the hosting
platform rather than running these commands through a shell. See
[`docs/DEPLOY.md`](docs/DEPLOY.md) for the Render and Neon setup.

## Configuration

Key DonorLink settings live in `config/donorlink.php` and can be overridden by
environment variables:

| Variable | Default | Purpose |
| --- | --- | --- |
| `DONORLINK_BOOKING_CUTOFF_MINUTES` | `60` | Minutes before a slot start when booking is closed |
| `DONORLINK_MAIL_DAILY_BUDGET` | `250` | Rolling 24-hour email limit |
| `DONORLINK_MAIL_TRANSACTIONAL_RESERVE` | `100` | Email quota reserved for transactional messages |
| `DONORLINK_MAIL_ANNOUNCEMENT_CHUNK` | `40` | Announcements dispatched per scheduled run |
| `DONORLINK_RECORDING_GRACE_DAYS` | `7` | Post-drive window for recording walk-in outcomes |
| `DONORLINK_LOW_FILL_THRESHOLD` | `0.25` | Low-fill dashboard alert threshold |
| `TRUSTED_PROXIES` | `*` | Reverse proxies trusted for HTTPS detection |

The remaining privacy, retention, and contact settings are documented in
[`docs/DEPLOY.md`](docs/DEPLOY.md).

## Brevo SMTP

Production email can use Brevo SMTP:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your-brevo-username
MAIL_PASSWORD=your-brevo-smtp-key
MAIL_FROM_ADDRESS=no-reply@example.ug
MAIL_FROM_NAME="DonorLink UG"
```

Port 587 uses SMTP with automatic STARTTLS negotiation.

Test delivery after configuring credentials:

```bash
php artisan donorlink:test-email "operator@example.com"
```

Never commit mail credentials or an application key to Git.

## Production deployment

Use MySQL or PostgreSQL, database-backed sessions and cache, and a database
queue worker. Before a deployment:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan donorlink:preflight
```

For first-deployment bootstrap, an initial administrator must be created by
invitation:

```bash
php artisan donorlink:create-admin --invite --name="UBTS System Admin" --email="admin@ubts.go.ug"
```

For complete hosted deployment instructions, see
[`docs/DEPLOY.md`](docs/DEPLOY.md).

## Documentation

- [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) - system modules and data model
- [`docs/DEPLOY.md`](docs/DEPLOY.md) - deployment and environment configuration
- [`docs/RUNBOOK.md`](docs/RUNBOOK.md) - operational incident procedures
- [`docs/LAUNCH_CHECKLIST.md`](docs/LAUNCH_CHECKLIST.md) - production launch checks
- [`docs/SMOKE_TEST.md`](docs/SMOKE_TEST.md) - manual staging verification

## License

This project is open source under the MIT License.
