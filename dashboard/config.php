<?php
// ---------------------------------------------------------
// HPHQ Dashboard — Credentials Config
// ---------------------------------------------------------
// To update credentials, generate a new hash by running:
//   php -r "echo password_hash('yourNewPassword', PASSWORD_DEFAULT);"
// Then replace the value of PASSWORD_HASH below.
// ---------------------------------------------------------

define('AUTH_USERNAME', 'hphque16');

define('AUTH_PASSWORD_HASH', '$2y$12$dWF9uIo4bSQspfKnOEPWv.TKSnJERpxJneCXfsSDiCETiM78MCTmm');

// Session lifetime in seconds (default: 8 hours)
define('SESSION_LIFETIME', 28800);
