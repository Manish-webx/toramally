<?php
/**
 * Bootstrap: loads configuration, starts a secure session and the helpers.
 * Every request passes through here (see /index.php).
 */
declare(strict_types=1);

define('ROOT', dirname(__DIR__));
define('APP', __DIR__);

if (!is_file(APP . '/config.php')) {
    http_response_code(500);
    exit('Configuration missing. Copy app/config.sample.php to app/config.php and fill in your database details.');
}
$GLOBALS['config'] = require APP . '/config.php';
$cfg = $GLOBALS['config'];

date_default_timezone_set($cfg['timezone'] ?? 'Asia/Kolkata');
mb_internal_encoding('UTF-8');

if (($cfg['env'] ?? 'production') === 'development') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', ROOT . '/storage/logs/php-error.log');
}

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
define('IS_HTTPS', $https);

if (!empty($cfg['force_https']) && !$https && PHP_SAPI !== 'cli') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
    exit;
}

if (PHP_SAPI !== 'cli') {
    session_name('tm_sess');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax',
    ]);
    session_start();
    // Basic security headers (also set in .htaccess where supported).
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

require APP . '/lib/db.php';
require APP . '/lib/helpers.php';
require APP . '/lib/security.php';
require APP . '/lib/catalogue.php';
require APP . '/lib/view.php';
require APP . '/lib/uploads.php';
