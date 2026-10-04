<?php
/**
 * The shared page frame. Every page is printed inside this file.
 * Head tags → header.php (logo, menus, drawers) → page content → footer.php.
 */
$tmConfig = [
    'base' => base_path(), 'csrf' => csrf_token(), 'cur' => current_currency(),
    'currencies' => currencies(), 'rounding' => currency_rounding(),
    'whatsapp' => preg_replace('/\D/', '', setting('whatsapp')),
    'threshold' => (int) setting('commission_threshold', 85000),
    'ga' => setting('ga_id'), 'pixel' => setting('meta_pixel_id'),
    'crafts' => array_map(fn($c) => ['slug' => $c['slug'], 'name' => $c['name'], 'scale' => $c['scale_word'], 'tag' => $c['tagline'], 'what' => $c['description']], crafts()),
];
$org = ['@context' => 'https://schema.org', '@graph' => [
    ['@type' => 'Organization', 'name' => 'Tōramally', 'alternateName' => 'Toramally', 'url' => base_url() . '/', 'slogan' => 'Crafted in silence.',
     'sameAs' => array_values(array_filter([setting('instagram'), setting('facebook')]))],
    ['@type' => 'ShoeStore', 'name' => 'Tōramally Kolkata', 'address' => ['@type' => 'PostalAddress', 'streetAddress' => setting('address_store'), 'addressLocality' => 'Kolkata', 'addressRegion' => 'West Bengal', 'addressCountry' => 'IN']],
]];
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($meta['title']) ?></title>
<meta name="description" content="<?= e($meta['description']) ?>">
<link rel="canonical" href="<?= e($meta['canonical']) ?>">
<meta name="theme-color" content="#1F3D2B">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Tōramally">
<meta property="og:title" content="<?= e($meta['title']) ?>">
<meta property="og:description" content="<?= e($meta['description']) ?>">
<meta property="og:url" content="<?= e($meta['canonical']) ?>">
<?php if (!empty($meta['og_image'])): ?><meta property="og:image" content="<?= e($meta['og_image']) ?>"><?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='8' fill='%231F3D2B'/%3E%3Cpath d='M10 32c8-11 22-14 34-8l10-7-3 15 3 15-10-7c-12 6-26 3-34-8z' fill='none' stroke='%239C7A3C' stroke-width='3' stroke-linejoin='round'/%3E%3Ccircle cx='20' cy='30' r='2.4' fill='%239C7A3C'/%3E%3C/svg%3E">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Jost:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= asset('css/site.css') ?>">
<script type="application/ld+json"><?= json_encode($org, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
<?php if (!empty($meta['json_ld'])): ?><script type="application/ld+json"><?= json_encode($meta['json_ld'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script><?php endif; ?>
</head>
<body class="<?= e($meta['body_class'] ?? '') ?>"<?= !empty($scrollTo) ? ' data-scroll="' . e($scrollTo) . '"' : '' ?>>
<?php require ROOT . '/templates/layout/header.php'; ?>
<main id="app" tabindex="-1">
<?= $content ?>
</main>
<?php require ROOT . '/templates/layout/footer.php'; ?>
<script>window.TM=<?= json_encode($tmConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?>;</script>
<script src="<?= asset('js/draw.js') ?>"></script>
<script src="<?= asset('js/site.js') ?>" defer></script>
</body>
</html>
