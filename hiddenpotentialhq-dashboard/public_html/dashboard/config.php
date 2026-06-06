<?php
// ---------------------------------------------------------
// HPHQ Dashboard — Credentials Config
// ---------------------------------------------------------
// To update credentials, generate a new hash by running:
//   php -r "echo password_hash('yourNewPassword', PASSWORD_DEFAULT);"
// Then replace the value of PASSWORD_HASH below.
// ---------------------------------------------------------

define('AUTH_USERNAME', 'CHANGE_ME');

// Replace this hash with: password_hash('yourPassword', PASSWORD_DEFAULT)
define('AUTH_PASSWORD_HASH', 'CHANGE_ME_RUN_HASH_COMMAND');

// Session lifetime in seconds (default: 8 hours)
define('SESSION_LIFETIME', 28800);
