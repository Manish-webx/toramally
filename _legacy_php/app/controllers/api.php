<?php
/**
 * JSON endpoints used by the site's JavaScript.
 *   POST /api/form        enquiries: contact, appointment, wedding, restoration, oneofone, designers, order_request
 *   POST /api/commission  bespoke builder submissions
 *   POST /api/newsletter  Letters from the House
 *   GET  /api/search?q=   search panel
 */
function api_dispatch(string $action, string $method): void
{
    if ($action === 'search' && $method === 'GET') {
        json_out(['ok' => true, 'results' => search_site((string) ($_GET['q'] ?? ''))]);
    }
    if ($method !== 'POST') json_out(['ok' => false, 'error' => 'Not allowed.'], 405);
    csrf_check();
    if (is_spam()) json_out(['ok' => true]); // pretend success to bots
    match ($action) {
        'form'       => api_form(),
        'commission' => api_commission(),
        'newsletter' => api_newsletter(),
        default      => json_out(['ok' => false, 'error' => 'Not found.'], 404),
    };
}

/** Which fields each form may send, and which are required. */
function form_schema(): array
{
    return [
        'contact'       => ['req' => ['name', 'email', 'type', 'message'], 'opt' => ['phone', 'country']],
        'appointment'   => ['req' => ['name', 'contact', 'type', 'location', 'date'], 'opt' => ['time', 'notes']],
        'wedding'       => ['req' => ['name', 'contact', 'date', 'for'], 'opt' => ['pairs', 'notes']],
        'restoration'   => ['req' => ['name', 'contact', 'service'], 'opt' => ['notes']],
        'oneofone'      => ['req' => ['name', 'email', 'idea'], 'opt' => ['phone', 'country']],
        'designers'     => ['req' => ['name', 'email', 'type', 'message'], 'opt' => ['company', 'linesheet']],
        'order_request' => ['req' => ['name', 'contact', 'items'], 'opt' => ['gift', 'currency']],
    ];
}

function api_form(): void
{
    $kind = (string) ($_POST['kind'] ?? '');
    $schema = form_schema()[$kind] ?? null;
    if (!$schema) json_out(['ok' => false, 'error' => 'Unknown form.'], 400);
    // Forms count every submission: at most 8 per hour from one visitor.
    if (!rate_limit_ok('form', $kind, 8, 60)) json_out(['ok' => false, 'error' => 'Too many messages. Please try again later or message us on WhatsApp.'], 429);

    $data = [];
    foreach (array_merge($schema['req'], $schema['opt']) as $f) {
        $data[$f] = trim(mb_substr((string) ($_POST[$f] ?? ''), 0, $f === 'items' ? 5000 : 2000));
    }
    $missing = array_values(array_filter($schema['req'], fn($f) => $data[$f] === ''));
    if ($missing) json_out(['ok' => false, 'error' => 'Please complete the highlighted fields.', 'fields' => $missing], 422);
    if (isset($data['email']) && $data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        json_out(['ok' => false, 'error' => 'Please enter a valid email address.', 'fields' => ['email']], 422);
    }

    $email = $data['email'] ?? (filter_var($data['contact'] ?? '', FILTER_VALIDATE_EMAIL) ? $data['contact'] : null);
    $id = db_insert('enquiries', [
        'kind' => $kind, 'name' => $data['name'] ?? null, 'email' => $email,
        'phone' => $data['phone'] ?? ($email ? null : ($data['contact'] ?? null)),
        'payload_json' => json_encode($data, JSON_UNESCAPED_UNICODE), 'ip' => client_ip(),
    ]);
    $up = save_uploads('files', 'enquiry', $id);
    rate_limit_hit('form', $kind);
    if (!$up['ok']) json_out(['ok' => false, 'error' => $up['error'], 'fields' => ['files']], 422);
    json_out(['ok' => true, 'message' => 'Received. A member of the House will reply within one working day.']);
}

function api_commission(): void
{
    if (!rate_limit_ok('form', 'commission', 8, 60)) json_out(['ok' => false, 'error' => 'Too many requests. Please try again later.'], 429);
    $name = trim(mb_substr((string) ($_POST['name'] ?? ''), 0, 120));
    $contact = trim(mb_substr((string) ($_POST['contact'] ?? ''), 0, 190));
    $build = json_decode((string) ($_POST['build'] ?? ''), true);
    $fields = [];
    if ($name === '') $fields[] = 'name';
    if ($contact === '') $fields[] = 'contact';
    if ($fields || !is_array($build)) json_out(['ok' => false, 'error' => 'Please add your name and a way to reach you.', 'fields' => $fields], 422);
    // Keep only known keys from the builder.
    $allowed = ['sil', 'colour', 'hex', 'craft', 'art', 'custom', 'initials', 'place', 'gold', 'nails', 'size', 'notes'];
    $build = array_intersect_key($build, array_flip($allowed));
    $ref = setting('commission_prefix', 'CM-') . date('y') . str_pad((string) (1 + (int) db_val('SELECT COUNT(*) FROM commissions')), 4, '0', STR_PAD_LEFT);
    $id = db_insert('commissions', [
        'ref' => $ref, 'name' => $name, 'contact' => $contact,
        'build_json' => json_encode($build, JSON_UNESCAPED_UNICODE),
        'estimate_inr' => max(0, (int) ($_POST['estimate'] ?? 0)),
    ]);
    $up = save_uploads('files', 'commission', $id);
    rate_limit_hit('form', 'commission');
    if (!$up['ok']) json_out(['ok' => false, 'error' => $up['error']], 422);
    json_out(['ok' => true, 'ref' => $ref, 'message' => 'Received. Your reference is ' . $ref . '. A member of the House will reply within one working day.']);
}

function api_newsletter(): void
{
    $email = trim((string) ($_POST['email'] ?? ''));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) json_out(['ok' => false, 'error' => 'Please enter a valid email address.'], 422);
    if (!rate_limit_ok('form', 'newsletter', 10, 60)) json_out(['ok' => false, 'error' => 'Please try again later.'], 429);
    db_exec("INSERT INTO subscribers (email, source) VALUES (?, 'footer') ON DUPLICATE KEY UPDATE status = 'subscribed'", [mb_strtolower($email)]);
    rate_limit_hit('form', 'newsletter');
    json_out(['ok' => true, 'message' => 'Thank you. A letter will find you soon.']);
}
