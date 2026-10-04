<?php
/**
 * Rendering. A page template is rendered first, then wrapped in the shared
 * layout (head, header, navigation, footer) so a change to the layout
 * updates every page.
 */

/**
 * @param string $template  file in /templates/pages without .php
 * @param array  $vars      variables for the template
 * @param array  $meta      title, description, canonical, og_image, body_class, json_ld
 */
function render(string $template, array $vars = [], array $meta = []): void
{
    $vars['meta'] = $meta;
    extract($vars, EXTR_SKIP);
    ob_start();
    require ROOT . '/templates/pages/' . $template . '.php';
    $content = ob_get_clean();
    $meta += [
        'title'       => setting('seo_home_title', 'Tōramally'),
        'description' => setting('seo_home_desc', ''),
        'canonical'   => base_url() . strtok($_SERVER['REQUEST_URI'] ?? '/', '?'),
        'nav'         => '',
    ];
    require ROOT . '/templates/layout/layout.php';
}

/** Include a partial with its own variables. */
function partial(string $name, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    require ROOT . '/templates/partials/' . $name . '.php';
}

/* ---------- Visual helpers ----------
 * Until photographs are uploaded in the admin, products are shown as
 * drawings made in the browser (assets/js/draw.js). Once a product has
 * images, real photographs are used automatically.
 */
function draw_slot(array $opts, string $alt, string $class = ''): string
{
    return '<div class="draw ' . e($class) . '" data-draw="' . e(json_encode($opts, JSON_UNESCAPED_UNICODE)) . '" role="img" aria-label="' . e($alt) . '"></div>';
}
function macro_slot(string $craft, string $colour, string $alt = ''): string
{
    $label = $alt ?: ((craft_by_slug($craft)['name'] ?? $craft) . ' macro detail');
    return '<div class="draw macro-slot" data-macro="' . e(json_encode(['craft' => $craft, 'colour' => $colour])) . '" role="img" aria-label="' . e($label) . '"></div>';
}
/** Main visual for a product in a given colour: photograph if uploaded, otherwise drawing. */
function product_visual(array $p, ?string $colour = null, string $kind = 'hero'): string
{
    $colour = $colour ?: $p['colours'][0]['name'];
    $alt = $p['name'] . ', ' . $p['silhouette'] . ' in ' . $colour . ', ' . mb_strtolower($p['craft_name']) . ' by hand';
    foreach ($p['images'] as $img) {
        $colMatch = !$img['colour_id'] || ($colour && in_array($img['colour_id'], array_column(array_filter($p['colours'], fn($c) => $c['name'] === $colour), 'id')));
        if (($img['kind'] === $kind || $kind === 'any') && $colMatch) {
            return '<img src="' . e(url($img['path'])) . '" alt="' . e($img['alt'] ?: $alt) . '" loading="lazy" decoding="async" width="2000" height="2500">';
        }
    }
    if ($kind === 'macro') return macro_slot($p['craft'], $colour);
    return draw_slot(['shape' => $p['drawing']['shape'] ?? 'loafer', 'colour' => $colour, 'craft' => $p['craft'], 'art' => $p['drawing']['art'] ?? null], $alt);
}
