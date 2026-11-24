# Deploy DonorLink UG to Render with Neon

This project is configured for Render's Docker services and Neon PostgreSQL.
Neon is PostgreSQL-only; it cannot host MySQL. The `render.yaml` Blueprint
creates a web service, a database-backed queue worker, and a one-minute
scheduler. The database is external and must be created in Neon.

## 1. Create the Neon database

1. Create a Neon project and database in a region close to the Render Frankfurt
   region, where possible.
2. In the Neon Console, choose the production branch, database, and role, then
   copy its connection string. Use the pooled connection for the web service
   and queue worker if the app's connection count requires it. Include
   `sslmode=require` in the URL.
3. Keep the connection string private. Add it to Render as `DB_URL`; the
   Blueprint sets `DB_CONNECTION=pgsql`.

The Neon string should be in PostgreSQL URL form:

```text
postgresql://ROLE:PASSWORD@HOST/DBNAME?sslmode=require
```

Laravel reads the complete string from `DB_URL`. If the Neon Console's URL also
contains `channel_binding=require`, remove that query parameter for this app:
Laravel's PostgreSQL PDO connector does not forward it to libpq. Keep
`sslmode=require` so the connection still requires TLS. Do not split the URL
into MySQL variables or commit it to the repository.

## 2. Configure and deploy the Render Blueprint

1. Push this repository to a Git provider connected to Render.
2. In Render, create a **Blueprint** from the repository and select
   `render.yaml`.
3. Fill in the Blueprint's unsynced values in the `donorlink-production`
   environment group:
   - `APP_KEY`: generate with `php artisan key:generate --show` in a trusted
     local environment, then copy the output into Render. Never commit it.
   - `APP_URL`: use your intended public HTTPS URL. If Render assigns its
     `onrender.com` URL only after provisioning, set that URL in the
     environment group and redeploy before generating invitations or opening
     the site to users.
   - `DB_URL`: use the Neon PostgreSQL connection string.
   - `MAIL_USERNAME`, `MAIL_PASSWORD`, and `MAIL_FROM_ADDRESS`: use verified
     Brevo SMTP credentials and a verified sender address.
4. Review the service plans and regions, then apply the Blueprint.

The Blueprint creates three Render services (web, queue worker, scheduler).
Review Render's current pricing for each service before applying it.

Enter secrets in the Render Dashboard, not in this repository or local `.env`:
open **Environment Groups** → `donorlink-production` → add or update `DB_URL`,
then save and deploy the linked services. Use the same group for the Neon URL
and other production values.

The web service builds the PHP/Apache application and Vite assets from the
Dockerfile. Render runs `php artisan migrate --force` as its web service
pre-deploy command. The `/up` health check verifies that Laravel is serving
requests. The queue and scheduler services share the same production
environment group. The container entrypoint caches Laravel configuration at
startup using the service's runtime environment variables.

## 3. Bootstrap the first administrator

After the first successful deployment and migrations:

1. Open the Render web service's Shell and run:

   ```bash
   php artisan donorlink:create-admin --invite --name="UBTS System Admin" --email="admin@ubts.go.ug"
   ```

2. Retrieve the one-time signed invitation from the command output, open it,
   and complete account setup. Ensure SMTP delivery is working if the
   invitation is sent by email.
3. Run the production checks in the Render Shell:

   ```bash
   php artisan donorlink:preflight
   ```

   Resolve all reported failures before opening the site to donors.

The initial deployment intentionally runs migrations only: preflight requires
an active administrator and therefore cannot pass before the first account is
created. Run preflight after bootstrap and before launch.

After the administrator is active, update the web service's `preDeployCommand`
in `render.yaml` to run both checks on future deployments:

```yaml
preDeployCommand: php artisan migrate --force && php artisan donorlink:preflight
```

Commit that change and deploy it. Subsequent deployments will run preflight
after migrations and stop before startup if a production check fails.

## 4. Required environment settings

The Blueprint configures production mode, PostgreSQL, database-backed sessions,
cache and queues, HTTPS-only cookies, stderr logging, the trusted proxy, and
Brevo SMTP defaults. Configure these additional application values in the
Render environment group before launch:

| Variable | Purpose |
| --- | --- |
| `DONORLINK_PRIVACY_CONTACT_EMAIL` | Public privacy contact |
| `DONORLINK_PRIVACY_CONTACT_ADDRESS` | Public privacy contact address |
| `DONORLINK_PRIVACY_RETENTION_SUMMARY` | Published record-retention summary |
| `DONORLINK_PRIVACY_REMOVAL_PROCESS` | Account removal instructions |
| `DONORLINK_PRIVACY_LAST_UPDATED` | Privacy notice revision date |

Review the email budget defaults in `render.yaml` against the active Brevo
plan. Keep `DONORLINK_MAIL_DAILY_BUDGET` greater than
`DONORLINK_MAIL_TRANSACTIONAL_RESERVE`.

## 5. Runtime operations and persistent data

- Render's `donorlink-queue` worker runs
  `php artisan queue:work database --sleep=3 --tries=3 --timeout=90`.
- The `donorlink-scheduler` cron service runs `php artisan schedule:run` every
  minute. Confirm the scheduler and worker heartbeats from `/admin/system`.
- Laravel logs go to stderr and are available in Render service logs.
- The current app does not store user uploads. Render's container filesystem
  is ephemeral; if persistent uploads are added later, configure object
  storage (such as S3-compatible storage) rather than relying on local files.
- Set up Neon backups/restore procedures and test a restore to a non-production
  database. Never run `migrate:fresh`, `migrate:reset`, or production seeders.

## 6. Launch checklist

Before sharing the public URL:

- Confirm `APP_DEBUG=false`, `APP_ENV=production`, the canonical HTTPS
  `APP_URL`, and `SESSION_SECURE_COOKIE=true`.
- Confirm Neon connectivity, SSL, migrations, and the active administrator.
- Confirm SMTP sender verification and test a transactional email.
- Run `php artisan donorlink:preflight` and resolve all failures.
- Complete the staging flow in [SMOKE_TEST.md](SMOKE_TEST.md).
- Configure DNS authentication for Brevo and add/verify any custom Render
  domain.
