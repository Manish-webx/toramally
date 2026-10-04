<?php
/**
 * Security helpers: CSRF tokens, rate limiting and spam traps.
 */

/** One token per session. Every form and every POST from JavaScript sends it back. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}
/** Stop the request if the token is missing or wrong. */
function csrf_check(): void
{
    $sent = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        json_out(['ok' => false, 'error' => 'Your session expired. Please refresh the page and try again.'], 419);
    }
}

/**
 * Allow at most $max attempts per $minutes for this scope + identifier (and IP).
 * Returns true if the attempt may go ahead.
 */
function rate_limit_ok(string $scope, string $identifier, int $max, int $minutes): bool
{
    $since = date('Y-m-d H:i:s', time() - $minutes * 60);
    $n = (int) db_val(
        'SELECT COUNT(*) FROM login_attempts WHERE scope = ? AND (identifier = ? OR ip = ?) AND success = 0 AND created_at > ?',
        [$scope, $identifier, client_ip(), $since]
    );
    return $n < $max;
}
function rate_limit_hit(string $scope, string $identifier, bool $success = false): void
{
    db_insert('login_attempts', ['scope' => $scope, 'identifier' => substr($identifier, 0, 190), 'ip' => client_ip(), 'success' => $success ? 1 : 0]);
}

/** Honeypot: a hidden field real people never fill in. Bots usually do. */
function is_spam(): bool
{
    return !empty($_POST['website']);
}
