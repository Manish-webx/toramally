<?php
/**
 * Page controllers: fetch what a page needs, then render its template.
 */

function page_home(): void
{
    render('home', ['blocks' => content_blocks('home'), 'featured' => featured_products(6), 'press' => press_items()], ['nav' => 'home']);
}

/** Shop filters: the same definitions drive the filter rail, the counts and the query. */
function shop_filter_defs(): array
{
    $bands = [['Under 10k', 0, 9999], ['10k to 20k', 10000, 19999], ['20k to 35k', 20000, 34999], ['35k to 60k', 35000, 59999], ['60k and above', 60000, PHP_INT_MAX]];
    return [
        'craft'      => ['Craft', array_map(fn($c) => [$c['slug'], $c['name']], array_filter(crafts(), fn($c) => $c['slug'] !== 'bespoke')), fn($p, $v) => $p['craft'] === $v],
        'collection' => ['Collection', array_map(fn($c) => [$c['slug'], $c['name']], editorial_collections()), fn($p, $v) => in_array($v, $p['collections'], true)],
        'sil'        => ['Silhouette', null, fn($p, $v) => $p['silhouette'] === $v],
        'price'      => ['Price (INR)', array_map(fn($b) => [$b[0], $b[0]], $bands), function ($p, $v) use ($bands) { foreach ($bands as $b) if ($b[0] === $v) return $p['base_price'] >= $b[1] && $p['base_price'] <= $b[2]; return false; }],
        'avail'      => ['Availability', [['Ready to Ship', 'Ready to Ship'], ['Made to Order', 'Made to Order'], ['Commission', 'Commission']], fn($p, $v) => $p['availability'] === $v],
        'colour'     => ['Colour', null, fn($p, $v) => in_array($v, array_column($p['colours'], 'name'), true)],
        'occasion'   => ['Occasion', [['Everyday', 'Everyday'], ['Wedding', 'Wedding'], ['Evening', 'Evening'], ['Statement', 'Statement']], fn($p, $v) => in_array($v, $p['occasion_list'], true)],
        'size'       => ['Size', array_map(fn($s) => [$s, $s], ['UK 4', 'UK 5', 'UK 6', 'UK 7', 'UK 8', 'UK 9', 'UK 10', 'UK 11']), fn($p, $v) => $p['size_type'] !== 'none' && ($p['availability'] !== 'Ready to Ship' || in_array($v, $p['ready'], true))],
    ];
}

function page_shop(array $seg): void
{
    $cats = ['men' => 'Men', 'women' => 'Women', 'accessories' => 'Accessories', 'everyday' => 'Everyday'];
    $catKey = $seg[1] ?? '';
    if ($catKey !== '' && !isset($cats[$catKey])) { page_404(); return; }
    $cat = $cats[$catKey] ?? null;

    // /shop/{cat}/{silhouette}/{product} → product page
    if (count($seg) === 4) {
        $p = product_by_slug($seg[3]);
        if (!$p || strtolower($p['category']) !== $catKey) { page_404(); return; }
        if (strtok($_SERVER['REQUEST_URI'], '?') !== product_url($p)) redirect(product_url($p), 301);
        page_product($p); return;
    }

    $line = null; $silFromPath = null;
    if (isset($seg[2])) {
        if ($cat === 'Men' && $seg[2] === 'classic') $line = 'Classic';
        elseif ($cat === 'Men' && $seg[2] === 'special-occasion') $line = 'Special Occasion';
        else $silFromPath = $seg[2];
    }
    $pool = array_values(array_filter(published_products(["category <> 'Service'"]), fn($p) => (!$cat || $p['category'] === $cat) && (!$line || $p['line'] === $line)));
    if ($silFromPath) {
        $match = array_filter($pool, fn($p) => slugify($p['silhouette']) . 's' === $silFromPath);
        if (!$match) { page_404(); return; }
        $_GET['sil'] = reset($match)['silhouette'];
    }

    $defs = shop_filter_defs();
    $defs['sil'][1] = array_map(fn($s) => [$s, $s], array_values(array_unique(array_column($pool, 'silhouette'))));
    $colours = []; foreach ($pool as $p) foreach ($p['colours'] as $c) $colours[$c['name']] = $c['hex'];
    $defs['colour'][1] = array_map(fn($n) => [$n, $n], array_keys($colours));

    $sel = [];
    foreach ($defs as $k => $_) $sel[$k] = array_values(array_filter(explode('|', (string) ($_GET[$k] ?? ''))));
    $apply = function (array $list, ?string $skip = null) use ($defs, $sel) {
        return array_values(array_filter($list, function ($p) use ($defs, $sel, $skip) {
            foreach ($defs as $k => [, , $fn]) {
                if ($k === $skip || !$sel[$k]) continue;
                $ok = false; foreach ($sel[$k] as $v) if ($fn($p, $v)) { $ok = true; break; }
                if (!$ok) return false;
            }
            return true;
        }));
    };
    $results = $apply($pool);
    $sort = $_GET['sort'] ?? 'featured';
    usort($results, match ($sort) {
        'low'  => fn($a, $b) => $a['base_price'] <=> $b['base_price'],
        'high' => fn($a, $b) => $b['base_price'] <=> $a['base_price'],
        'new'  => fn($a, $b) => strcmp($b['created_at'], $a['created_at']) ?: $b['id'] <=> $a['id'],
        default => fn($a, $b) => [$b['featured'], $a['sort']] <=> [$a['featured'], $b['sort']],
    });
    $counts = [];
    foreach ($defs as $k => [, $opts, $fn]) {
        $base = $apply($pool, $k);
        foreach ($opts as [$v]) $counts[$k][$v] = count(array_filter($base, fn($p) => $fn($p, $v)));
    }
    $title = $cat ? ($line ? "$cat, $line" : $cat) : 'All pairs';
    $sub = ['Men' => 'Classic signature designs, and pairs for the occasions that matter.', 'Women' => 'Heels, flats and mules, painted and marked by hand.',
            'Accessories' => 'Belts, wallets and collectibles in the house finishes.', 'Everyday' => 'Velvet and slippers. Soft pairs for every day.'][$cat] ?? 'Every pair in the house, ready to ship or made to order.';
    render('shop', compact('cat', 'catKey', 'line', 'pool', 'results', 'defs', 'sel', 'counts', 'sort', 'title', 'sub', 'colours'), [
        'title' => $title . ' | Handcrafted in India | Tōramally',
        'description' => $sub,
        'canonical' => base_url() . strtok($_SERVER['REQUEST_URI'], '?'),
        'nav' => 'shop',
    ]);
}

function page_collection(string $slug): void
{
    $c = collection_by_slug($slug);
    if (!$c) { page_404(); return; }
    if ($c['type'] === 'craft') redirect('craft/' . $slug, 301);
    redirect('shop?collection=' . rawurlencode($slug));
}

function page_product(array $p): void
{
    $related = array_slice(array_values(array_filter(published_products(["category <> 'Service'"]), fn($x) => $x['id'] !== $p['id'] && ($x['craft'] === $p['craft'] || $x['silhouette'] === $p['silhouette']))), 0, 4);
    $craft = craft_by_slug($p['craft']);
    $ld = ['@context' => 'https://schema.org', '@type' => 'Product', 'name' => 'Tōramally ' . $p['name'], 'description' => $p['poetic'],
           'brand' => ['@type' => 'Brand', 'name' => 'Tōramally'], 'sku' => $p['slug'],
           'offers' => ['@type' => 'Offer', 'priceCurrency' => 'INR', 'price' => $p['base_price'], 'url' => base_url() . substr(product_url($p), strlen(base_path())),
                        'availability' => $p['status'] === 'Sold' ? 'https://schema.org/SoldOut' : ($p['availability'] === 'Ready to Ship' ? 'https://schema.org/InStock' : 'https://schema.org/PreOrder')]];
    render('product', compact('p', 'related', 'craft'), [
        'title' => $p['seo_title'] ?: ($p['name'] . ': ' . $p['silhouette'] . ', ' . $p['craft_name'] . ' | Tōramally'),
        'description' => $p['seo_desc'] ?: mb_substr($p['poetic'] . ' ' . ($p['construction'] ? $p['construction'] . '. ' : '') . 'Handcrafted in India.', 0, 155),
        'canonical' => base_url() . substr(product_url($p), strlen(base_path())),
        'json_ld' => $ld, 'nav' => 'shop', 'body_class' => 'is-product',
    ]);
}

function page_craft_index(): void
{
    render('craft-index', [], ['title' => 'The craft ladder | Tōramally', 'description' => 'Velvet, Patina, Scarring, Miniature, Tattoo, Carving and Bespoke. Seven levels of hand.', 'nav' => 'craft']);
}
function page_craft(string $slug): void
{
    $c = craft_by_slug($slug);
    if (!$c) { page_404(); return; }
    $pairs = array_values(array_filter(published_products(["category <> 'Service'"]), fn($p) => $p['craft'] === $slug));
    render('craft', compact('c', 'pairs'), ['title' => $c['seo_title'] ?: ($c['name'] . ': ' . $c['tagline'] . ' | Tōramally'), 'description' => $c['seo_desc'] ?: mb_substr((string) $c['description'], 0, 155), 'nav' => 'craft']);
}

function page_bespoke(string $sub): void
{
    switch ($sub) {
        case '':           render('bespoke', [], ['title' => 'Bespoke | Made for you | Tōramally', 'description' => 'Build your pair: silhouette, colour, craft, artwork and initials, with a live estimate.', 'nav' => 'bespoke']); return;
        case 'build':      render('builder', ['from' => product_by_slug((string) ($_GET['from'] ?? ''))], ['title' => 'Build your pair | Tōramally', 'description' => 'Choose a silhouette, colour, craft, artwork and initials, and see the estimate as you go.', 'nav' => 'bespoke']); return;
        case 'wedding':    render('wedding', [], ['title' => 'Wedding shoes for the groom and bride | Tōramally', 'description' => 'Pairs for the groom, the bride and the family. Check whether your wedding date can be met.', 'nav' => 'bespoke']); return;
        case 'one-of-one': render('enquiry', ['kind' => 'oneofone'], ['title' => 'One of One | Tōramally', 'description' => 'Exceptional and collector pieces, made once.', 'nav' => 'bespoke']); return;
        case 'designers':  render('enquiry', ['kind' => 'designers'], ['title' => 'Designers & Collectors | Tōramally', 'description' => 'For designers, retailers, stylists, film, corporate gifting and collaborations.', 'nav' => 'bespoke']); return;
        case 'custom-colour': case 'personalisation': case 'custom-artwork':
            render('bespoke', ['scrollTo' => $sub], ['title' => 'Bespoke | Tōramally', 'nav' => 'bespoke']); return;
    }
    page_404();
}

function page_house(string $sub): void
{
    if ($sub === 'worn-by') {
        $people = db_all('SELECT * FROM celebrities WHERE visible = 1 AND consent_on_file = 1 ORDER BY sort, id');
        render('worn-by', compact('people'), ['title' => 'Worn by | Tōramally', 'description' => 'Clients and friends of the house, in their Tōramally pairs.', 'nav' => 'house']); return;
    }
    if ($sub === 'videos') {
        $videos = db_all('SELECT * FROM videos WHERE visible = 1 ORDER BY sort, id');
        render('videos', compact('videos'), ['title' => 'Videos | Tōramally', 'description' => 'Films of the making: brush, beam, needle and blade.', 'nav' => 'house']); return;
    }
    $sections = ['', 'story', 'lucknow', 'kolkata', 'workshop', 'artisans', 'philosophy', 'materials', 'press', 'stockists'];
    if (!in_array($sub, $sections, true)) { page_404(); return; }
    render('house', ['scrollTo' => $sub, 'press' => press_items()], ['title' => 'The House | Lucknow and Kolkata | Tōramally', 'description' => 'Our story, our workshop in Lucknow and our flagship in Kolkata.', 'nav' => 'house', 'canonical' => base_url() . '/house']);
}

function page_journal(): void
{
    render('journal', ['posts' => journal_posts(), 'soon' => journal_posts('In preparation')], ['title' => 'Journal | Tōramally', 'description' => 'Notes on craft, style, materials, people and places.', 'nav' => 'journal']);
}
function page_article(string $slug): void
{
    $a = journal_by_slug($slug);
    if (!$a) { page_404(); return; }
    $craft = craft_by_id((int) $a['craft_id']);
    $related = $craft ? array_slice(array_values(array_filter(published_products(), fn($p) => $p['craft'] === $craft['slug'])), 0, 3) : [];
    render('article', compact('a', 'craft', 'related'), [
        'title' => ($a['seo_title'] ?: $a['title']) . ' | Tōramally Journal', 'description' => $a['seo_desc'] ?: $a['dek'], 'nav' => 'journal',
        'json_ld' => ['@context' => 'https://schema.org', '@type' => 'Article', 'headline' => $a['title'], 'datePublished' => $a['published_at'], 'publisher' => ['@type' => 'Organization', 'name' => 'Tōramally']],
    ]);
}

function page_policy(string $slug): void
{
    $pg = page_by_slug($slug);
    if (!$pg) { page_404(); return; }
    render('policy', compact('pg'), ['title' => ($pg['seo_title'] ?: $pg['title']) . ' | Tōramally', 'description' => $pg['seo_desc'] ?: '']);
}

function page_simple(string $tpl, string $title, string $desc): void
{
    render($tpl, [], ['title' => $title, 'description' => $desc, 'nav' => in_array($tpl, ['visit', 'contact'], true) ? 'house' : '']);
}

function page_search(string $q): void
{
    render('search', ['q' => $q, 'results' => search_site($q)], ['title' => 'Search | Tōramally', 'description' => '']);
}

function page_404(): void
{
    http_response_code(404);
    render('404', [], ['title' => 'Page not found | Tōramally', 'description' => '']);
}
