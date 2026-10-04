<?php
/**
 * Small helpers used across templates.
 */

/** Escape text for HTML. Use on every value printed into a page. */
function e($v): string
{
    return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Read a site setting (edited in Admin > Settings). Cached per request. */
function setting(string $key, $default = '')
{
    static $all = null;
    if ($all === null) {
        $all = [];
        foreach (db_all('SELECT k, v FROM settings') as $r) $all[$r['k']] = $r['v'];
    }
    return (isset($all[$key]) && $all[$key] !== '') ? $all[$key] : $default;
}

/** Base URL of the site, e.g. https://toramally.com (no trailing slash). */
function base_url(): string
{
    static $b = null;
    if ($b !== null) return $b;
    $cfg = $GLOBALS['config']['base_url'] ?? '';
    if ($cfg) return $b = rtrim($cfg, '/');
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $b = (IS_HTTPS ? 'https://' : 'http://') . $host . $dir;
}

/** Path prefix when the site lives in a sub-folder (usually empty). */
function base_path(): string
{
    return rtrim((string) parse_url(base_url(), PHP_URL_PATH), '/');
}

/** Build a site-relative URL: url('shop/men') → /shop/men */
function url(string $path = ''): string
{
    return base_path() . '/' . ltrim($path, '/');
}

/** Asset URL with a version stamp so browsers pick up changes. */
function asset(string $path): string
{
    $file = ROOT . '/assets/' . ltrim($path, '/');
    $v = is_file($file) ? filemtime($file) : 1;
    return url('assets/' . ltrim($path, '/')) . '?v=' . $v;
}

function redirect(string $to, int $code = 302): void
{
    header('Location: ' . (preg_match('#^https?://#', $to) ? $to : url($to)), true, $code);
    exit;
}

function json_out($data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function slugify(string $s): string
{
    $s = strtr($s, ['ā' => 'a', 'ō' => 'o', 'ū' => 'u', 'ī' => 'i', 'Ā' => 'a', 'Ō' => 'o']);
    $s = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $s), '-'));
    return $s ?: 'item';
}

/* ---------- Currency ----------
 * Prices are stored in INR. Other currencies use the fixed table in
 * Settings (reviewed monthly), so a price never changes between visits.
 * Format rule: "INR 23614" — no decimals, no "Rs.", no tax text.
 */
function currencies(): array
{
    return json_decode(setting('currencies_json', '{"INR":1}'), true) ?: ['INR' => 1];
}
function currency_rounding(): array
{
    return json_decode(setting('rounding_json', '{"INR":1}'), true) ?: ['INR' => 1];
}
function current_currency(): string
{
    $c = $_COOKIE['tm_cur'] ?? 'INR';
    return array_key_exists($c, currencies()) ? $c : 'INR';
}
function convert_price(int $inr, ?string $cur = null): int
{
    $cur = $cur ?: current_currency();
    if ($cur === 'INR') return $inr;
    $rate = (float) (currencies()[$cur] ?? 1);
    $step = (int) (currency_rounding()[$cur] ?? 1) ?: 1;
    return (int) (round($inr / $rate / $step) * $step);
}
function money_text(int $inr, ?string $cur = null): string
{
    $cur = $cur ?: current_currency();
    return $cur . ' ' . convert_price($inr, $cur);
}
/** Price as HTML. The data-inr attribute lets the currency switch update it without reloading. */
function money(int $inr): string
{
    return '<span class="money" data-inr="' . $inr . '">' . e(money_text($inr)) . '</span>';
}

/** WhatsApp link with a pre-filled message. */
function wa_link(string $text): string
{
    $n = preg_replace('/\D/', '', setting('whatsapp'));
    return 'https://wa.me/' . $n . '?text=' . rawurlencode($text);
}

/** Visitor IP (for rate limiting and records). */
function client_ip(): string
{
    return substr($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', 0, 45);
}
