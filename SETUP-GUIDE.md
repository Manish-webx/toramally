# Tōramally website — setup guide (Stage 1)

This guide gets the PHP version of the site running on your hosting. Stage 1 gives you
the full site on PHP and MySQL, with every form saving to the database. The admin panel
(to see and manage everything) arrives in Stage 2; the guide grows with each stage.

## What you need

- Hosting with **PHP 8.1 or newer** and **MySQL** (Hostinger, GoDaddy and Bluehost all qualify).
- Your domain pointed at that hosting, with the free SSL certificate switched on.
- About 20 minutes.

## Step 1. Create the database

1. In your hosting panel (hPanel on Hostinger, cPanel on GoDaddy and Bluehost), open **Databases → MySQL Databases**.
2. Create a database (for example `toramally`), a database user and a strong password.
3. Give that user **All privileges** on the database.
4. Write down the four values: host (usually `localhost`), database name, user, password.

## Step 2. Import the tables

1. Open **phpMyAdmin** from the hosting panel and click your new database on the left.
2. Open the **Import** tab, choose `install/schema.sql`, and press **Go**.
3. Do the same again with `install/seed.sql`. This adds the craft ladder, collections, sample products, press, journal and policies.

## Step 3. Upload the files

1. Open **File Manager** and go into `public_html` (the folder your domain shows).
2. Upload the zip and use **Extract**, so `index.php` and `.htaccess` sit directly inside `public_html`.
   - If you don't see `.htaccess`, turn on "Show hidden files" in File Manager settings.
3. In the `app` folder, make a copy of `config.sample.php` and name it `config.php`.
4. Edit `config.php`:
   - Fill in the database host, name, user and password from Step 1.
   - Replace `CHANGE-ME-TO-64-RANDOM-CHARACTERS` with 64 random letters and numbers (any password generator will do). Keep a copy somewhere safe and **never change it after launch**: from Stage 4 it locks your payment and courier keys.
5. Make sure the `storage` folder is writable (permission 755 is usual; File Manager → right-click → Permissions).

## Step 4. Check it works

Visit your domain. You should see the home page. Then try:

- `yourdomain.com/shop/men` (listing and filters)
- A product page, choose a size and **Add to Bag**
- `yourdomain.com/contact` and send a test message
- `yourdomain.com/app/config.php` must show **Forbidden** (this proves private files are protected)

Your test message is saved in the `enquiries` table (phpMyAdmin → Browse). From Stage 2 you'll see it in the admin panel instead.

## Step 5. Turn on HTTPS everywhere

Once your SSL certificate is active, open `app/config.php` and set `'force_https' => true`.

## If something goes wrong

- **"The site is resting for a moment"**: the database details in `config.php` are wrong, or the database user doesn't have privileges.
- **Home page works, every other page shows "Not Found"**: `.htaccess` is missing or wasn't extracted. Check hidden files are shown.
- **Blank page**: set `'env' => 'development'` in `config.php` to see the error, then set it back to `'production'`.

## Your contact details

Until the admin panel arrives in Stage 2, the WhatsApp number, phone, email, store hours and
announcement line are in the `settings` table (phpMyAdmin → `settings` → Browse → edit the `v` column).
