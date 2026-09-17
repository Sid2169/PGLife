# PG Life — Project Report

## 1. Overview

PG Life is a PG (Paying Guest) accommodation search web application. It helps students and working
professionals discover PG listings in a city, view detailed information (photos, amenities, ratings,
rent, gender type), and shortlist properties they are interested in. Shortlisted properties are
saved against the user's account and accessible from their dashboard.

**Assignment module covered here:** dynamic PHP + MySQL backend, AJAX-based interactions (no page
reloads), a React.js component for the property listing, a database schema file (`pglife.sql`),
and deployment documentation.

## 2. Features

- **Property Listing (React)** — properties are rendered as a React component that fetches data
  from the backend via AJAX. Each card shows photo, name, address, location, price, gender icon,
  star rating and the number of interested users.
- **Filters without reload** — city (via navigation), gender (male / female / unisex), budget
  range (min–max rent) and sort (lowest / highest rent first) are applied instantly through AJAX,
  with a loading spinner shown while results are fetched.
- **Property Details** — photo gallery, description, amenity icons, cleanliness / food / safety
  ratings, and testimonials rendered from the database.
- **Mark Interested / Shortlist** — users can click the heart icon on a card or the details page to
  add/remove a property from their shortlist **without a page reload**. Guests are prompted to log in.
- **Authentication** — sign up / log in (email + password) and logout; session-based.
- **Dashboard** — profile information plus the list of shortlisted properties; un-hearting a
  property removes its card from the dashboard instantly.
- **Responsive layout** — Bootstrap-based, works on mobile and desktop.

## 3. Tech Stack

| Layer    | Technology                              |
|----------|-----------------------------------------|
| Frontend | HTML, CSS, Bootstrap, jQuery, React 18 + Babel (vendored under `js/vendor/`) |
| Backend  | PHP 7+                                   |
| Database | MySQL                                    |
| Server   | Apache (XAMPP-compatible)                |
| AJAX     | jQuery `$.post` / `fetch` API            |

## 4. Project Structure

```
PGLife/
├── api/
│   ├── filter_properties.php     # JSON feed for the React listing (city/gender/budget/sort)
│   ├── login_submit.php          # login endpoint
│   ├── signup_submit.php         # signup endpoint
│   ├── logout.php                # ends session
│   └── toggle_interested.php     # AJAX toggle of "interested" (insert/delete)
├── css/                          # per-page stylesheets
├── img/                          # images, amenity icons (img/amenities/*.svg), property photos (img/properties/<id>/*)
├── includes/
│   ├── config.php                # DB credentials from env vars (XAMPP defaults)
│   ├── database_connect.php      # MySQL connection
│   ├── functions.php             # output escaping + CSRF helpers (boots session)
│   ├── session.php               # hardened session bootstrap
│   ├── footer.php                # footer + shared JS (jquery, bootstrap, app.js)
│   ├── header.php                # navbar
│   ├── head_links.php            # common CSS links + CSRF meta tag
│   ├── login_modal.php / signup_modal.php
├── js/
│   ├── app.js                    # shared AJAX heart-toggle + loading overlay
│   ├── property_list_app.jsx     # React component for the property listing
│   └── vendor/                   # vendored React + Babel production builds
├── .htaccess                     # Apache hardening (no indexes, blocks .sql/.md)
├── dashboard.php                 # profile + shortlisted properties
├── index.php                     # landing page with city tiles
├── property_detail.php           # full property detail page
├── property_list.php             # React property list page
└── pglife.sql                    # database schema + seed data (deliverable)
```

## 5. Database Schema

Defined in `pglife.sql` (full CREATE + seed). Entity-relationship summary:

```
cities (id, name)
  ──< properties (id, city_id, name, address, description, gender, rent, rating_clean, rating_food, rating_safety)
users (id, email, password, full_name, phone, gender, college_name)
amenities (id, name, type, icon)
properties_amenities (property_id, amenity_id)      -- M:N junction
interested_users_properties (user_id, property_id)  -- M:N junction (shortlist)
testimonials (id, property_id, user_name, content)
```

- `gender` on `properties` is ENUM('male','female','unisex'); the listing applies the rule
  *male → (male, unisex)*, *female → (female, unisex)*, *unisex → (unisex)*.
- Passwords are stored as bcrypt hashes (`password_hash` / `password_verify`).
- Property photos are resolved from the filesystem (`img/properties/<id>/*`), amenity icons from
  `img/amenities/*.svg`.
- Demo account seeded: `demo@pglife.in` / `password`.

## 6. Setup / Run Locally

1. Install XAMPP (or Apache + PHP 7 + MySQL).
2. Start Apache and MySQL services.
3. Import the database: open http://localhost/phpmyadmin → *Import* → choose `pglife.sql`, or run
   `mysql -u root -p < pglife.sql`.
4. Copy/place the project under the web root (e.g. `htdocs/PGLife`).
5. Ensure the DB credentials in `includes/database_connect.php` match your MySQL
   (`127.0.0.1`, user `root`, password `password`, database `pglife`).
6. Open http://localhost/PGLife/ and click any city tile to browse PGs.

   DB credentials are read from `includes/config.php`. It uses the environment variables
   `DB_HOST`, `DB_USER`, `DB_PASS` and `DB_NAME`, falling back to the local XAMPP defaults
   (`127.0.0.1`, `root`, `password`, `pglife`) when they are not set, so no code edit is
   needed for the default local setup.

## 7. React Integration

`property_list.php` hosts the React app. The listing component (`js/property_list_app.jsx`) uses
hooks state for the active filters and fetches `api/filter_properties.php` on every filter change
(debounced), updating the UI instantly while a spinner is displayed. Clicking a heart posts to
`api/toggle_interested.php` (with the session CSRF token) and updates the card's fill + interested
count in place; unauthenticated users get the login modal. `api/filter_properties.php` applies
gender/budget/sort filtering in SQL and returns only relevant columns as JSON. React and Babel are
served from the local `js/vendor/` directory, so the listing works without any external CDN.

## 8. AJAX Interactions (no page reload)

| Interaction                  | Endpoint                        | UI behaviour                              |
|------------------------------|---------------------------------|--------------------------------------------|
| Apply filters (gender/budget/sort) | `api/filter_properties.php` | Re-renders listing, spinner shown |
| Mark / unmark interested     | `api/toggle_interested.php`     | Heart fills/empties, count updates; card removed on dashboard |

## 9. Screenshots to Capture

1. **Listing page** — React-rendered PG cards for a city.
2. **Detail page** — gallery, amenity grid, ratings, testimonials.
3. **AJAX interaction** — a) clicking the heart without page reload, b) applying a filter and
   observing the spinner + updated list, c) toggling interest from the dashboard.

## 10. Deployment Notes (live server)

1. Register on a PHP/MySQL host (e.g. a standard LAMP shared host or a VPS).
2. Upload the project files to the web root via FTP/SSH.
3. Create a MySQL database + user on the host and import `pglife.sql`.
4. Set the DB credentials through environment variables (`DB_HOST`, `DB_USER`, `DB_PASS`,
   `DB_NAME`) or edit `includes/config.php` if the host does not support env vars.
5. Point your domain (or the host-provided URL) to the web root and verify http/https.
6. Serve the site over HTTPS. The included `.htaccess` (and `includes/.htaccess`) disables
   directory listing and blocks direct download of `pglife.sql`/docs on Apache hosts; mirror
   those rules on nginx.
7. Security notes: all queries use prepared statements, output is HTML-escaped, passwords are
   bcrypt hashed, sessions use HTTP-only/SameSite cookies, and login/signup/toggle calls are
   CSRF-protected.

## 11. Deliverables

- Live URL: *(add deployed URL once hosting credentials are provided)*
- Source: https://github.com/Sid2169/PGLife
- Database schema: `pglife.sql` (this repository)
- Screenshots & this document.