# Files — Stage 1 (PHP conversion)

Everything below is new: the site moved from one HTML file to PHP + MySQL.
Upload the whole folder to `public_html`.

## Public (served to visitors)
| File | What it does |
| --- | --- |
| `index.php` | Front door. Every address goes here and is sent to the right page. Add new pages here. |
| `.htaccess` | Clean addresses, blocks private folders, security headers, compression and caching. |
| `assets/css/site.css` | All styles. Colours and type sizes are at the top. Unchanged design. |
| `assets/js/draw.js` | Draws the shoe illustrations and craft textures until photographs are uploaded. |
| `assets/js/site.js` | Menus, search, currency switch, bag, product page, filters, builder, wedding date check, forms. |
| `uploads/` | Product and content images uploaded from the admin (Stage 3). Scripts can't run here. |
| `router-dev.php` | For testing on a computer only. Not used on hosting; safe to delete. |

## Private (blocked from visitors)
| File | What it does |
| --- | --- |
| `app/config.sample.php` | Copy to `config.php` and add database details. |
| `app/bootstrap.php` | Starts every request: settings, secure session, helpers. |
| `app/lib/db.php` | Database access. All queries are prepared statements (SQL-injection safe). |
| `app/lib/helpers.php` | Escaping, settings, URLs, currency formatting ("INR 23614"), WhatsApp links. |
| `app/lib/security.php` | CSRF tokens, rate limits, spam trap. |
| `app/lib/catalogue.php` | Crafts, collections, products, press, journal, pages, search. |
| `app/lib/view.php` | Wraps each page in the shared layout; photograph-or-drawing helper. |
| `app/lib/uploads.php` | Safe handling of "Upload ref. Image" files (type check, 20 MB, 5 files, private storage). |
| `app/controllers/pages.php` | Prepares each page (shop filters, product, craft, bespoke, house, journal, policies). |
| `app/controllers/api.php` | Receives forms, commissions, newsletter sign-ups and search. |
| `templates/layout/layout.php` | Shared page frame: head tags, SEO, structured data. |
| `templates/layout/header.php` | Shared header, menus, mobile drawer, bag drawer, search panel. |
| `templates/layout/footer.php` | Shared footer, mobile tab bar, cookie notice. |
| `templates/partials/*.php` | Reusable pieces: product card, craft ladder, ladder indicator, breadcrumb, page heading, form, upload field, WhatsApp button. |
| `templates/pages/*.php` | One file per page type (24 pages). |
| `storage/` | Customer uploads and error logs. Never served directly. |
| `install/schema.sql` | All 37 database tables, for every stage. Import first. |
| `install/seed.sql` | Starting content: settings, craft ladder, collections, 19 sample products, press, journal, policies. |
