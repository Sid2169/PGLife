# PG Life

**Live demo:** https://pglife-production-a9c9.up.railway.app · **Source:** https://github.com/Sid2169/PGLife

PG Life is a [PG (paying guest)](https://en.wikipedia.org/wiki/Paying_guest_(domestic)) accommodation
search web app for students and working professionals: browse PG listings in a city, filter them by
gender, budget and rent, view photos, amenities and ratings, and shortlist the ones you like.

## Features

- **Property listing in React** — a city's PGs are rendered as a React component that fetches data
  from the PHP backend over AJAX (spinner shown while loading, no page reloads).
- **Instant filters** — gender (male / female / unisex), budget range and sort (rent ascending /
  descending) applied via `api/filter_properties.php`, all in SQL.
- **Shortlist / "interested"** — heart any property from the listing, detail page or dashboard to
  add/remove it from your profile; guests are prompted to log in.
- **Property details** — photo gallery, amenity icons, cleanliness / food / safety ratings and
  testimonials.
- **Authentication** — email + password signup/login, session-based, logout.
- **Dashboard** — profile info plus your shortlisted properties (un-hearting removes the card live).
- **Responsive UI** — Bootstrap-based, works on mobile and desktop.

## Architecture

Standard LAMP, with a React component layered on top of the PHP pages:

```
                 (server-rendered pages)
   index.php ── dashboard.php ── property_list.php ── property_detail.php
                      │                  │                    │
                      └────────── includes/ ──────────────────┘
                   config.php · database_connect.php · functions.php · session.php
                   header.php · footer.php · login/signup modal
                                   │
                          (AJAX, JSON, no reload)
   js/app.js ── heart toggles ──► api/toggle_interested.php
   js/property_list_app.jsx ──► api/filter_properties.php      ◄── React 18 + Babel (js/vendor/)
                                   │
                                   ▼
                        MySQL  (schema in pglife.sql)
```

| Layer    | Technology |
|----------|------------|
| Frontend | HTML, CSS, Bootstrap, jQuery, React 18 + Babel (**vendored** under `js/vendor/`, no CDN) |
| Backend  | PHP 7.4+ (mysqli prepared statements) |
| Database | MySQL 5.7+ / MariaDB 10.3+ |
| Server   | Apache |

**Data model** (`pglife.sql`): `cities 1─n properties`, `users`, `amenities`,
`properties_amenities` (M:N) and `interested_users_properties` (M:N shortlist), `testimonials`.
Passwords are stored as bcrypt hashes.

## Running locally (XAMPP)

1. Start Apache and MySQL in XAMPP.
2. Import the schema: <http://localhost/phpmyadmin> → **Import** → `pglife.sql`
   (or `mysql -u root < pglife.sql`).
3. Copy the project into `htdocs/PGLife` and open <http://localhost/PGLife/>.

DB credentials are read from environment variables (`DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASS`,
`DB_NAME`) with XAMPP defaults (`127.0.0.1`, `root`, `password`, `pglife`) — no code edits needed
for the default local setup.

## Deployment

Currently live on **Railway** at **https://pglife-production-a9c9.up.railway.app** — deployed from
this repo via `Dockerfile` (Apache + PHP 8.2 + MySQL service). Key point: Railway's managed MySQL
creates a database named `railway`, but `pglife.sql` installs into `pglife`, so the web service must
set `DB_NAME=pglife`. Full instructions for Railway, cPanel, VPS and Docker Compose are in
[DEPLOYMENT.md](DEPLOYMENT.md).

## Security notes

- All SQL uses prepared statements; all output is HTML-escaped via a shared `e()` helper.
- Passwords hashed with bcrypt (`password_hash` / `password_verify`).
- Login, signup and shortlist calls are CSRF-protected (session tokens).
- Hardened sessions (HTTP-only, SameSite cookies, `Secure` on HTTPS).
- `.htaccess` blocks directory listing and direct download of `pglife.sql`, configs and docs
  (verified returning `403` on the live deployment).

## Try it

Login with the seeded demo account: **`demo@pglife.in` / `password`**