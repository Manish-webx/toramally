<?php
/**
 * Catalogue: crafts, collections, products, press and journal.
 * All reads go through these functions so templates stay simple.
 */

/** The craft ladder, in order. */
function crafts(): array
{
    static $c = null;
    return $c ??= db_all("SELECT * FROM collections WHERE type='craft' AND active=1 ORDER BY sort, id");
}
function craft_by_slug(string $slug): ?array
{
    foreach (crafts() as $c) if ($c['slug'] === $slug) return $c;
    return null;
}
function craft_by_id(?int $id): ?array
{
    foreach (crafts() as $c) if ((int) $c['id'] === (int) $id) return $c;
    return null;
}
/** Position of a craft on the ladder (0-based). */
function craft_index(string $slug): int
{
    foreach (crafts() as $i => $c) if ($c['slug'] === $slug) return $i;
    return 0;
}
function editorial_collections(): array
{
    return db_all("SELECT * FROM collections WHERE type='collection' AND active=1 ORDER BY sort, id");
}
function collection_by_slug(string $slug): ?array
{
    return db_one('SELECT * FROM collections WHERE slug = ? AND active = 1', [$slug]);
}

/** Default colour of the macro tile for each craft. */
function craft_colour(string $slug): string
{
    return ['velvet' => 'Forest Green', 'patina' => 'Cognac', 'scarring' => 'Dark Brown', 'miniature' => 'Oxblood',
            'tattoo' => 'Tan', 'carving' => 'Tobacco', 'bespoke' => 'Black'][$slug] ?? 'Oxblood';
}

/** Attach colours, ready stock, images and craft to a list of product rows. */
function hydrate_products(array $rows): array
{
    if (!$rows) return [];
    $ids = array_column($rows, 'id');
    $in = rtrim(str_repeat('?,', count($ids)), ',');
    $cols = $stock = $imgs = $colls = [];
    foreach (db_all("SELECT * FROM product_colours WHERE product_id IN ($in) ORDER BY sort, id", $ids) as $r) $cols[$r['product_id']][] = $r;
    foreach (db_all("SELECT * FROM product_stock WHERE product_id IN ($in) AND qty > 0 ORDER BY id", $ids) as $r) $stock[$r['product_id']][] = $r;
    foreach (db_all("SELECT * FROM product_images WHERE product_id IN ($in) ORDER BY sort, id", $ids) as $r) $imgs[$r['product_id']][] = $r;
    foreach (db_all("SELECT pc.product_id, c.slug FROM product_collection pc JOIN collections c ON c.id = pc.collection_id WHERE pc.product_id IN ($in)", $ids) as $r) $colls[$r['product_id']][] = $r['slug'];
    foreach ($rows as &$p) {
        $p['colours'] = $cols[$p['id']] ?? [['name' => 'Black', 'hex' => '#1f1c19', 'id' => null, 'price_diff' => 0]];
        $p['ready'] = array_values(array_unique(array_column($stock[$p['id']] ?? [], 'size')));
        $p['images'] = $imgs[$p['id']] ?? [];
        $p['collections'] = $colls[$p['id']] ?? [];
        $craft = craft_by_id((int) $p['craft_id']);
        $p['craft'] = $craft ? $craft['slug'] : 'patina';
        $p['craft_name'] = $craft ? $craft['name'] : '';
        $p['drawing'] = json_decode($p['drawing_json'] ?: '{}', true) ?: [];
        $p['occasion_list'] = array_filter(array_map('trim', explode(',', (string) $p['occasions'])));
    }
    return $rows;
}

/** Products visible in the shop. Filtering happens in shop_filter(). */
function published_products(array $where = [], array $params = []): array
{
    $sql = "SELECT * FROM products WHERE status IN ('Published','Sold')";
    foreach ($where as $w) $sql .= ' AND ' . $w;
    $sql .= ' ORDER BY featured DESC, sort, id';
    return hydrate_products(db_all($sql, $params));
}
function product_by_slug(string $slug): ?array
{
    $r = db_all("SELECT * FROM products WHERE slug = ? AND status IN ('Published','Sold')", [$slug]);
    return $r ? hydrate_products($r)[0] : null;
}
function featured_products(int $n = 6): array
{
    return array_slice(published_products(['featured = 1']), 0, $n);
}

/** Canonical product URL: /shop/men/belgian-loafers/taus (one URL per product). */
function product_url(array $p): string
{
    if ($p['category'] === 'Service') return url($p['slug']);
    return url('shop/' . strtolower($p['category']) . '/' . slugify($p['silhouette']) . 's/' . $p['slug']);
}
function availability_label(array $p): string
{
    return $p['availability'] === 'Made to Order'
        ? 'Made to Order, ' . $p['lead_min'] . ' to ' . $p['lead_max'] . ' weeks'
        : $p['availability'];
}

/** Size options for a product, stored as UK sizes (or waist for belts). */
function size_options(array $p): array
{
    if ($p['size_type'] === 'belt') return ['80 cm', '85 cm', '90 cm', '95 cm', '100 cm', '105 cm', '110 cm'];
    if ($p['size_type'] === 'none') return [];
    $women = $p['size_type'] === 'shoe_women';
    $out = [];
    for ($n = $women ? 3 : 5; $n <= ($women ? 9 : 12); $n += 0.5) $out[] = 'UK ' . rtrim(rtrim(number_format($n, 1), '0'), '.');
    return $out;
}

function press_items(): array
{
    return db_all('SELECT * FROM press_items WHERE visible = 1 ORDER BY sort, id');
}
function journal_posts(string $status = 'Published'): array
{
    return db_all('SELECT * FROM journal_posts WHERE status = ? ORDER BY published_at DESC, id', [$status]);
}
function journal_by_slug(string $slug): ?array
{
    return db_one("SELECT * FROM journal_posts WHERE slug = ? AND status = 'Published'", [$slug]);
}
function page_by_slug(string $slug): ?array
{
    return db_one('SELECT * FROM pages WHERE slug = ?', [$slug]);
}
/** Homepage blocks keyed by block_key, in admin order, hidden ones removed. */
function content_blocks(string $page = 'home'): array
{
    $out = [];
    foreach (db_all('SELECT * FROM content_blocks WHERE page = ? AND visible = 1 ORDER BY sort, id', [$page]) as $b) {
        $out[$b['block_key']] = json_decode($b['data_json'] ?: '{}', true) ?: [];
    }
    return $out;
}

/** Shop search used by the search panel. Ignores accents ("toramally" finds "Tōramally"). */
function search_site(string $term): array
{
    $t = mb_strtolower(trim($term));
    if ($t === '') return [];
    $syn = ['loafer' => ['slip-on', 'belgian', 'penny', 'loafer', 'mule'], 'demule' => ['mule'], 'whole cut' => ['wholecut'],
            'laser' => ['scarring'], 'painted' => ['miniature'], 'tattooed' => ['tattoo'], 'wedding' => ['wedding', 'special occasion', 'peshawari'],
            'groom' => ['wedding', 'peshawari'], 'sherwani' => ['wedding', 'peshawari'], 'monogram' => ['initials', 'personalisation'],
            'initials' => ['personalisation', 'initials']];
    $norm = fn($s) => strtr(mb_strtolower((string) $s), ['ā' => 'a', 'ō' => 'o', 'ū' => 'u', 'ī' => 'i']);
    $t = $norm($t);
    $terms = [$t];
    foreach ($syn as $k => $v) if (str_contains($t, $k)) $terms = array_merge($terms, $v);
    $words = preg_split('/\s+/', $t);
    $match = function (string $hay) use ($terms, $words) {
        foreach ($terms as $q) if (str_contains($hay, $q)) return true;
        $all = true; foreach ($words as $w) if (!str_contains($hay, $w)) { $all = false; break; }
        if ($all) return true;
        foreach ($words as $w) {
            if (strlen($w) < 5) continue;
            foreach (preg_split('/\W+/', $hay) as $x) if ($x !== '' && levenshtein($w, $x) <= 1) return true;
        }
        return false;
    };
    $res = ['Products' => [], 'Crafts' => [], 'Journal' => [], 'Pages' => []];
    foreach (published_products() as $p) {
        $hay = $norm(implode(' ', [$p['name'], $p['silhouette'], $p['category'], $p['line'], $p['craft_name'], $p['occasions'], $p['poetic'], implode(' ', array_column($p['colours'], 'name')), implode(' ', $p['collections'])]));
        if ($match($hay)) $res['Products'][] = ['t' => $p['name'] . ', ' . $p['silhouette'], 'h' => product_url($p)];
    }
    foreach (crafts() as $c) if ($match($norm($c['name'] . ' ' . $c['tagline'] . ' ' . $c['description']))) $res['Crafts'][] = ['t' => $c['name'] . ', ' . mb_strtolower($c['tagline']), 'h' => url('craft/' . $c['slug'])];
    foreach (journal_posts() as $j) if ($match($norm($j['title'] . ' ' . $j['dek'] . ' ' . strip_tags((string) $j['body_html'])))) $res['Journal'][] = ['t' => $j['title'], 'h' => url('journal/' . $j['slug'])];
    $pages = [['Wedding', 'bespoke/wedding', 'wedding groom bride sherwani date'], ['Build your pair', 'bespoke/build', 'bespoke custom builder commission initials monogram personalisation colour'],
              ['Size guide', 'size-guide', 'size fit measure'], ['Care', 'care', 'care polish clean monsoon'], ['Restoration', 'restoration', 'restore resole repair'],
              ['Visit and appointments', 'visit', 'store kolkata lucknow appointment visit address'], ['Shipping', 'shipping', 'shipping duties delivery'],
              ['Returns and exchange', 'returns', 'returns exchange'], ['Our story', 'house/story', 'story founder rahul lakme history']];
    foreach ($pages as [$title, $path, $k]) if ($match($norm($title . ' ' . $k))) $res['Pages'][] = ['t' => $title, 'h' => url($path)];
    return array_map(fn($a) => array_slice($a, 0, 6), $res);
}
