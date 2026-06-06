# HPHQ Dashboard — Deployment Guide

## What you're deploying

```
public_html/
└── dashboard/
    ├── .htaccess      ← access rules
    ├── config.php     ← hashed credentials (fill in before uploading)
    ├── auth.php       ← session guard (included by protected pages)
    ├── login.php      ← login form
    ├── logout.php     ← session teardown
    └── index.html     ← your dashboard (you add this manually)
```

---

## Step 1 — Generate your password hash

Before uploading anything, generate a bcrypt hash of your password.

**Option A — Hostinger terminal (if available)**
In hPanel → Advanced → SSH Terminal:
```
php -r "echo password_hash('yourPasswordHere', PASSWORD_DEFAULT) . PHP_EOL;"
```

**Option B — Any PHP-enabled server or local PHP install**
```
php -r "echo password_hash('yourPasswordHere', PASSWORD_DEFAULT) . PHP_EOL;"
```

**Option C — Online (trusted environment only)**
Use https://bcrypt-generator.com (Cost factor 12 is fine). Copy the full output string.

The hash looks like: `$2y$10$...` (60 characters).

---

## Step 2 — Edit config.php

Open `config.php` and replace the two placeholder values:

```php
define('AUTH_USERNAME', 'yourChosenUsername');
define('AUTH_PASSWORD_HASH', '$2y$10$...paste-your-hash-here...');
```

Save the file. Do not change anything else.

---

## Step 3 — Upload files via Hostinger File Manager

1. Log in to **hPanel** → **Files** → **File Manager**
2. Navigate to `public_html/`
3. Create a folder named `dashboard` if it doesn't exist yet
4. Open the `dashboard/` folder
5. Upload these files **in this order**:

   | File | Notes |
   |---|---|
   | `.htaccess` | Upload first. If File Manager hides dotfiles, enable "Show Hidden Files" in the toolbar. |
   | `config.php` | The one you edited in Step 2. |
   | `auth.php` | No edits needed. |
   | `login.php` | No edits needed. |
   | `logout.php` | No edits needed. |
   | `index.html` | Your dashboard page — drop it in last. |

---

## Step 4 — Test the login

1. Visit `https://hiddenpotentialhq.com/dashboard/` in an incognito window
2. You should be redirected to `login.php` automatically (or manually go to `/dashboard/login.php`)
3. Enter your username and password
4. On success you land on `index.html`
5. Visit `https://hiddenpotentialhq.com/dashboard/config.php` directly — you should get a **403 Forbidden** (the `.htaccess` block is working)

---

## Adding a logout link in your dashboard

Paste this anywhere in your `index.html`:
```html
<a href="logout.php">Log out</a>
```

---

## Protecting index.html with PHP session checking

Static `.html` files cannot run PHP. If you want the dashboard itself to require a valid session (redirect to login if someone goes directly to `/dashboard/index.html` without logging in), rename it `index.php` and add this as the very first line:

```php
<?php require_once __DIR__ . '/../dashboard/auth.php'; ?>
```

Because `index.php` is inside the `dashboard/` folder, `__DIR__` resolves to that folder, so the path `../dashboard/auth.php` is equivalent to `./auth.php`. You can simplify it to:

```php
<?php require_once __DIR__ . '/auth.php'; ?>
```

Full top of file:
```php
<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
...rest of your dashboard HTML...
```

Then upload `index.php` instead of `index.html`. Hostinger serves `index.php` as the directory default.

---

## Updating credentials later

1. Generate a new hash (Step 1 above) with your new password
2. Open `config.php` in File Manager → Edit
3. Replace `AUTH_USERNAME` and/or `AUTH_PASSWORD_HASH`
4. Save — takes effect immediately (no restart needed)

All active sessions remain valid until they expire (default: 8 hours) or the user logs out. To force everyone out immediately, also change the session cookie name: open `auth.php` and `login.php`, find `hphq_auth` and `hphq_expires`, and rename them to something new (e.g. `hphq_auth2`).

---

## Troubleshooting

| Symptom | Fix |
|---|---|
| Redirects loop between login.php and itself | PHP sessions may be disabled. Check hPanel → PHP settings → `session.save_handler` is set to `files` |
| `.htaccess` not taking effect | Confirm Apache `AllowOverride` is on. Hostinger shared hosting enables this by default. |
| Login succeeds but redirects to a 404 | `index.html` (or `index.php`) hasn't been uploaded yet |
| Can't see `.htaccess` in File Manager | Click the settings/gear icon and enable "Show Hidden Files" |
| `password_verify` always fails | Make sure the hash in `config.php` has no extra spaces or line breaks — copy it as a single unbroken string |
