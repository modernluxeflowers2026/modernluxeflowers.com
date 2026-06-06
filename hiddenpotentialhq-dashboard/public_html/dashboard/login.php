<?php
require_once __DIR__ . '/config.php';

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

session_start();

// Already logged in — send to dashboard
if (
    isset($_SESSION['hphq_auth'], $_SESSION['hphq_expires']) &&
    $_SESSION['hphq_auth'] === true &&
    time() < $_SESSION['hphq_expires']
) {
    header('Location: index.html');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (
        hash_equals(AUTH_USERNAME, $username) &&
        password_verify($password, AUTH_PASSWORD_HASH)
    ) {
        session_regenerate_id(true);
        $_SESSION['hphq_auth']    = true;
        $_SESSION['hphq_expires'] = time() + SESSION_LIFETIME;
        header('Location: index.html');
        exit;
    }

    // Same message for wrong username OR wrong password (no enumeration)
    $error = 'Invalid credentials. Please try again.';
    // Small delay to blunt brute-force attempts
    usleep(500000);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HPHQ — Dashboard Login</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: #1a1a1a;
      color: #FAF8F4;
      font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
    }

    .card {
      background: #242424;
      border: 1px solid #2e2e2e;
      border-radius: 10px;
      padding: 2.5rem 2rem;
      width: 100%;
      max-width: 380px;
    }

    .eyebrow {
      font-size: 0.7rem;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: #C94B2C;
      margin-bottom: 0.5rem;
    }

    h1 {
      font-size: 1.45rem;
      font-weight: 600;
      color: #FAF8F4;
      margin-bottom: 2rem;
      line-height: 1.2;
    }

    .field {
      margin-bottom: 1.1rem;
    }

    label {
      display: block;
      font-size: 0.75rem;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: #999;
      margin-bottom: 0.45rem;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      background: #1a1a1a;
      border: 1px solid #333;
      border-radius: 6px;
      color: #FAF8F4;
      font-size: 0.95rem;
      padding: 0.7rem 0.9rem;
      outline: none;
      transition: border-color 0.15s;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #C94B2C;
    }

    .error {
      background: rgba(201, 75, 44, 0.12);
      border: 1px solid rgba(201, 75, 44, 0.35);
      border-radius: 6px;
      color: #e07a62;
      font-size: 0.85rem;
      padding: 0.65rem 0.9rem;
      margin-bottom: 1.2rem;
    }

    button[type="submit"] {
      width: 100%;
      background: #C94B2C;
      color: #FAF8F4;
      border: none;
      border-radius: 6px;
      font-size: 0.9rem;
      font-weight: 600;
      letter-spacing: 0.04em;
      padding: 0.8rem 1rem;
      cursor: pointer;
      margin-top: 0.5rem;
      transition: background 0.15s, opacity 0.15s;
    }

    button[type="submit"]:hover { background: #b03f24; }
    button[type="submit"]:active { opacity: 0.85; }
  </style>
</head>
<body>
  <div class="card">
    <p class="eyebrow">Hidden Potential HQ</p>
    <h1>Dashboard Login</h1>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php" autocomplete="off" novalidate>
      <div class="field">
        <label for="username">Username</label>
        <input
          type="text"
          id="username"
          name="username"
          required
          autocomplete="username"
          autofocus
          value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
        />
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          required
          autocomplete="current-password"
        />
      </div>
      <button type="submit">Sign In</button>
    </form>
  </div>
</body>
</html>
