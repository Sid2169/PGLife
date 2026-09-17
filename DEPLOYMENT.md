# PG Life — Deployment Plan

This document is the actionable deployment plan for the hardened PG Life build.
It covers three hosting topologies, the exact steps, configuration reference,
verification, and rollback. Choose one topology and follow its track.

**Artifacts included in this repo:**

| File | Purpose |
|------|---------|
| Dockerfile, docker-compose.yml, .env.example, .dockerignore | Topology C (Docker) |
| docker-entrypoint.sh, railway.json | Topology D (PaaS / connect-GitHub) |
| `deploy/deploy-vps.sh` | Topology B release/rollback script |
| `deploy/apache/pglife.conf`, `deploy/nginx/pglife.conf` | Web-server configs |
| `.htaccess`, `includes/.htaccess` | Apache hardening (deny includes, `.env`, `.sql`, docs) |

---

## 1. Application requirements

| Requirement        | Value                                                                 |
|--------------------|-----------------------------------------------------------------------|
| Runtime            | PHP **7.4+** (uses `random_bytes`, `password_hash`, array-spread args; 8.x recommended) |
| PHP extensions     | `mysqli` (with **mysqlnd** for `mysqli_stmt_get_result`), `session`, `json`, `hash`, `filter` |
| Database           | MySQL 5.7+ / MariaDB 10.3+ (InnoDB, utf8mb4)                           |
| Web server         | Apache 2.4 (`AllowOverride All` for `.htaccess`) or nginx              |
| Disk               | ~50 MB (mostly `img/properties/`) + database                           |
| Outbound network   | Not required at runtime (React/Babel are vendored in `js/vendor/`)     |

---

## 2. Configuration reference

The app reads credentials from environment variables; `includes/config.php`
falls back to local XAMPP defaults when they are not set.

| Variable   | Default       | Purpose                    |
|------------|---------------|----------------------------|
| `DB_HOST`  | `127.0.0.1`   | MySQL host                 |
| `DB_USER`  | `root`        | MySQL user                 |
| `DB_PASS`  | `password`    | MySQL password             |
| `DB_NAME`  | `pglife`      | Database name              |
| `APP_ENV`  | `production`  | Environment marker         |

**Never commit real credentials.** Prefer env vars; if the host cannot set
them, edit `includes/config.php` directly on the server (it is blocked from
web access by `includes/.htaccess`).

---

## 3. Topology A — Shared LAMP hosting (cPanel/Plesk) — recommended for the assignment

1. **Build the release:** `git clone`/`git archive` the repo; exclude `.git`,
   `solutions/`, and `PROJECT_REPORT.md`. (`.htaccess` already blocks `.sql`/`.md`.)
2. **Create DB:** cPanel → *MySQL Databases* → create database + user, grant
   ALL. Note host/user/pass/name.
3. **Import schema:** phpMyAdmin → select DB → *Import* → upload `pglife.sql`.
4. **Upload files:** File Manager/FTP into `public_html` (or a subfolder).
5. **Set credentials:** cPanel → *Environment Variables* (if available) for
   `DB_*`; otherwise edit `includes/config.php`.
6. **Ensure `.htaccess` is honored:** confirm Apache allows `Options`/`AuthConfig`
   overrides (most shared hosts allow `All`). If `.htaccess` causes a 500,
   remove the `Options -Indexes` line and keep the `FilesMatch` block.
7. **HTTPS:** enable the host's free AutoSSL/Let's Encrypt; redirect HTTP→HTTPS
   from the host's domain settings (not `.htaccess`, to avoid loops).
8. **PHP version:** set the domain to PHP 7.4+ in *MultiPHP Manager*.

---

## 4. Topology B — VPS (Ubuntu + Apache + PHP-FPM + MySQL)

```bash
# Packages
sudo apt update
sudo apt install -y apache2 mysql-server php php-fpm php-mysql php-cli unzip

# Code
sudo mkdir -p /var/www/pglife
sudo chown -R www-data:www-data /var/www/pglife
# upload/clone the release into /var/www/pglife (web root = project root)

# Database
sudo mysql -e "CREATE DATABASE pglife CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'pglife'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD';"
sudo mysql -e "GRANT ALL PRIVILEGES ON pglife.* TO 'pglife'@'localhost'; FLUSH PRIVILEGES;"
sudo mysql pglife < /var/www/pglife/pglife.sql
```

Apache vhost: install the provided `deploy/apache/pglife.conf` (it already
targets `/var/www/pglife/current` and sets the `DB_*` env vars):

```bash
sudo cp deploy/apache/pglife.conf /etc/apache2/sites-available/pglife.conf
sudo nano /etc/apache2/sites-available/pglife.conf   # set ServerName + DB_PASS
sudo a2enmod rewrite headers
sudo a2ensite pglife && sudo systemctl reload apache2
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d pglife.example.com          # HTTPS + auto-renew
```

nginx alternative: install `deploy/nginx/pglife.conf` under
`/etc/nginx/sites-available/` and adjust `fastcgi_pass` to your PHP-FPM socket.

Hardening: `display_errors=Off`, `log_errors=On` in the PHP-FPM pool (already
set in the bundled Docker image); fail2ban optional; UFW allow 22/80/443 only.

---

## 5. Topology C — Docker Compose (reproducible)

Uses the bundled `Dockerfile`, `docker-compose.yml`, `.env.example` and
`.dockerignore`. The image installs `mysqli`, disables `display_errors` and
serves the app; MySQL imports `pglife.sql` automatically on first boot.

```bash
cp .env.example .env
# edit .env -> set DB_PASS and MYSQL_ROOT_PASSWORD
docker compose up -d --build
docker compose ps                     # wait for db "healthy"
# App is now on http://SERVER_IP:8080
```

Put a TLS-terminating reverse proxy (Caddy/Traefik/nginx) in front for HTTPS.

To rebuild after a code change: `docker compose up -d --build`. To reset the
database, `docker compose down -v` (destroys the volume) then bring it up again.

---

## 6. Topology D — PaaS "connect GitHub" (Railway / Render / Platform.sh)

**Vercel/Netlify are not suitable:** their PHP runs as stateless serverless
functions with no persistent filesystem and no bundled MySQL. This app needs a
long-lived MySQL connection and serves ~45 MB of images from disk.

Platforms that *do* fit, easiest first:

| Platform | How it works | MySQL | Notes |
|----------|--------------|-------|-------|
| **Railway** | Connect GitHub; detects the `Dockerfile`; one-click MySQL; auto HTTPS | Managed | Closest to Vercel. Usage-based, ~$5/mo + trial credit |
| **Platform.sh / Upsun** | Git-driven, PHP-native, managed MySQL, per-branch envs | Managed | Great PHP fit, pricier ($19+/mo) |
| **Faable** | Connect GitHub; plain PHP, Apache + `.htaccess`, auto SSL | Add-on | Zero-config, EU-hosted, newer |
| **Render** | Connect GitHub; builds the Dockerfile | **No managed MySQL** (Postgres only) | Run MySQL as a private disk service or use external MySQL |

### Railway, step by step

1. Push this repo to GitHub (the `Dockerfile`, `docker-entrypoint.sh` and
   `railway.json` are already included; the entrypoint maps Apache to the
   platform `$PORT`).
2. railway.app → **New Project → Deploy from GitHub repo** → pick the repo.
3. Click **+ New → Database → MySQL**. Wait until it is running.
4. Open the **web service → Variables** and add the DB settings. Use Railway's
   reference syntax so they follow the MySQL service:
   ```
   DB_HOST = ${{MySQL.MYSQLHOST}}
   DB_PORT = ${{MySQL.MYSQLPORT}}
   DB_NAME = ${{MySQL.MYSQLDATABASE}}
   DB_USER = ${{MySQL.MYSQLUSER}}
   DB_PASS = ${{MySQL.MYSQLPASSWORD}}
   APP_ENV = production
   ```
   (`includes/config.php` also auto-detects the raw `MYSQLHOST`/`MYSQLUSER`/…
   names, so referencing those directly works too.)
5. **Import the schema once.** The web service starts fine without it (the
   health check is the DB-free home page). Import via the Railway CLI:
   ```bash
   npm i -g @railway/cli
   railway login && railway link      # choose the project
   railway connect MySQL              # opens a mysql shell
   # then inside the mysql prompt:
   SOURCE /full/path/to/pglife.sql;
   ```
   Or copy the public connection URL from the MySQL service's **Connect** tab
   and run `mysql -h HOST -P PORT -u USER -p DB < pglife.sql` locally.
6. Generate a public domain for the web service (**Settings → Networking →
   Generate Domain**) and open it. Run the smoke test in section 7.



## 7. Post-deploy smoke test checklist

| # | Check | Expected |
|---|-------|----------|
| 1 | `GET /` | 200, city tiles render |
| 2 | `GET /property_list.php?city=Delhi` | spinner → PG cards (React) |
| 3 | `GET /api/filter_properties.php?city=Delhi&gender=male&sort=rent_asc` | JSON `success:true`, sorted |
| 4 | Signup a new account | account created; login works |
| 5 | Login as `demo@pglife.in` / `password` (fresh import only) | dashboard loads |
| 6 | Heart a property, reload | still shortlisted; count updates |
| 7 | `GET /includes/config.php` | **403 / denied** |
| 8 | `GET /pglife.sql` | **403 / denied** |
| 9 | Invalid `property_detail.php?property_id=abc` | “Invalid property” message |
| 10 | Browser console | no mixed-content / React errors |
| 11 | HTTPS | valid cert, no HTTP mixed content |

---

## 8. Operations

- **Backups:** nightly `mysqldump --single-transaction pglife | gzip` + retain
  last 7; snapshot `img/properties/` (uploads/content).
- **Logs:** Apache/PHP error logs; rotate via `logrotate`.
- **Uptime:** external HTTP check on `/` and `/api/filter_properties.php`.
- **Updates:** OS/PHP security patches monthly; re-run smoke test after.
- **Secrets:** rotate DB password at least annually; keep `config.php` out of Git.

## 9. Rollback

1. Keep the previous release in `releases/<timestamp>/` and switch a `current`
   symlink atomically (`ln -sfn releases/2026-09-17 current`).
2. If the DB schema changed, restore the pre-deploy dump:
   `mysql pglife < backup/pglife-<timestamp>.sql`.
3. Re-run smoke tests 1–6.

## 10. Optional CI/CD

- `git tag` a release; a deploy job `rsync`/`scp`s the archive to the host,
  runs `mysql < pglife.sql` only for first deploy, then reloads Apache.
- Run the PHP lint/tests in CI before tagging (see verification commands used
  during hardening: `php -l`, JSX transform check).

---

## 11. Known caveats

- **bcrypt migration:** a database created before this change still holds SHA-1
  hashes and those accounts cannot log in. Fresh `pglife.sql` import is the
  supported path; re-import or have users reset.
- **`mysqli_stmt_get_result` requires mysqlnd.** If a host lacks it, switch the
  affected reads to `mysqli_stmt_bind_result`/`store_result`.
- **`.htaccess` is Apache-only.** On nginx, deny `/includes/` and `*.sql`
  in the server block.
- **`session.cookie_secure` is not set yet.** Under HTTPS, set it in `php.ini`
  or the pool: `session.cookie_secure=1`, `session.cookie_httponly=1`.
- **Babel compiles JSX in the browser.** Fine here, but a production build
  pipeline (Vite/webpack) would remove the runtime compile cost.
