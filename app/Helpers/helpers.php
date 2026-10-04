<?php

use App\Models\Collection;
use App\Models\ContentBlock;
use App\Models\JournalPost;
use App\Models\Page;
use App\Models\PressItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Str;

if (!function_exists('setting')) {
    function setting(string $key, $default = '')
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('currencies')) {
    function currencies(): array
    {
        return json_decode(setting('currencies_json', '{"INR":1}'), true) ?: ['INR' => 1];
    }
}

if (!function_exists('currency_rounding')) {
    function currency_rounding(): array
    {
        return json_decode(setting('rounding_json', '{"INR":1}'), true) ?: ['INR' => 1];
    }
}

if (!function_exists('current_currency')) {
    function current_currency(): string
    {
        $c = request()->cookie('tm_cur', 'INR');
        return array_key_exists($c, currencies()) ? $c : 'INR';
    }
}

if (!function_exists('convert_price')) {
    function convert_price(int $inr, ?string $cur = null): int
    {
        $cur = $cur ?: current_currency();
        if ($cur === 'INR') return $inr;
        $rate = (float) (currencies()[$cur] ?? 1);
        $step = (int) (currency_rounding()[$cur] ?? 1) ?: 1;
        return (int) (round($inr / $rate / $step) * $step);
    }
}

if (!function_exists('money_text')) {
    function money_text(int $inr, ?string $cur = null): string
    {
        $cur = $cur ?: current_currency();
        return $cur . ' ' . convert_price($inr, $cur);
    }
}

if (!function_exists('money')) {
    function money(int $inr): string
    {
        return '<span class="money" data-inr="' . $inr . '">' . e(money_text($inr)) . '</span>';
    }
}

if (!function_exists('wa_link')) {
    function wa_link(string $text): string
    {
        $n = preg_replace('/\D/', '', setting('whatsapp'));
        return 'https://wa.me/' . $n . '?text=' . rawurlencode($text);
    }
}

if (!function_exists('client_ip')) {
    function client_ip(): string
    {
        return request()->ip() ?? '0.0.0.0';
    }
}

if (!function_exists('crafts')) {
    function crafts(): array
    {
        static $c = null;
        if ($c === null) {
            $c = Collection::crafts()->map(fn($item) => $item->toArray())->all();
        }
        return $c;
    }
}

if (!function_exists('craft_by_slug')) {
    function craft_by_slug(string $slug): ?array
    {
        foreach (crafts() as $c) {
            if ($c['slug'] === $slug) return $c;
        }
        return null;
    }
}

if (!function_exists('craft_by_id')) {
    function craft_by_id(?int $id): ?array
    {
        foreach (crafts() as $c) {
            if ((int) $c['id'] === (int) $id) return $c;
        }
        return null;
    }
}

if (!function_exists('craft_index')) {
    function craft_index(string $slug): int
    {
        foreach (crafts() as $i => $c) {
            if ($c['slug'] === $slug) return $i;
        }
        return 0;
    }
}

if (!function_exists('craft_colour')) {
    function craft_colour(string $slug): string
    {
        return [
            'velvet' => 'Forest Green',
            'patina' => 'Cognac',
            'scarring' => 'Dark Brown',
            'miniature' => 'Oxblood',
            'tattoo' => 'Tan',
            'carving' => 'Tobacco',
            'bespoke' => 'Black'
        ][$slug] ?? 'Oxblood';
    }
}

if (!function_exists('draw_slot')) {
    function draw_slot(array $opts, string $alt, string $class = ''): string
    {
        return '<div class="draw ' . e($class) . '" data-draw="' . e(json_encode($opts, JSON_UNESCAPED_UNICODE)) . '" role="img" aria-label="' . e($alt) . '"></div>';
    }
}

if (!function_exists('macro_slot')) {
    function macro_slot(string $craft, string $colour, string $alt = ''): string
    {
        $label = $alt ?: ((craft_by_slug($craft)['name'] ?? $craft) . ' macro detail');
        return '<div class="draw macro-slot" data-macro="' . e(json_encode(['craft' => $craft, 'colour' => $colour])) . '" role="img" aria-label="' . e($label) . '"></div>';
    }
}

if (!function_exists('product_visual')) {
    function product_visual($p, ?string $colour = null, string $kind = 'hero'): string
    {
        $pArr = is_array($p) ? $p : $p->toArray();
        $colours = $pArr['colours'] ?? [];
        if (empty($colours) && isset($pArr['id'])) {
            $colours = Product::find($pArr['id'])?->colours->toArray() ?? [];
        }
        $colour = $colour ?: ($colours[0]['name'] ?? 'Black');
        $craftName = $pArr['craft_name'] ?? ($pArr['craft']['name'] ?? '');
        $alt = ($pArr['name'] ?? '') . ', ' . ($pArr['silhouette'] ?? '') . ' in ' . $colour . ', ' . mb_strtolower($craftName) . ' by hand';
        
        $images = $pArr['images'] ?? [];
        if (empty($images) && isset($pArr['id'])) {
            $images = Product::find($pArr['id'])?->images->toArray() ?? [];
        }

        $matchedColourIds = array_column(array_filter($colours, fn($c) => $c['name'] === $colour), 'id');

        // Pass 1: Try finding specific variant image matching colour
        foreach ($images as $img) {
            $colMatch = !empty($img['colour_id']) && in_array($img['colour_id'], $matchedColourIds);
            $kindMatch = ($img['kind'] === $kind) || ($kind === 'any') || (in_array($kind, ['hero', 'side']) && in_array($img['kind'], ['hero', 'side', 'angle']));
            if ($colMatch && $kindMatch) {
                return '<img src="' . e(asset($img['path'])) . '" alt="' . e($img['alt'] ?: $alt) . '" loading="lazy" decoding="async" width="2000" height="2500">';
            }
        }

        // Pass 2: Try finding general image (colour_id is null / 0)
        foreach ($images as $img) {
            $isGeneral = empty($img['colour_id']);
            $kindMatch = ($img['kind'] === $kind) || ($kind === 'any') || (in_array($kind, ['hero', 'side']) && in_array($img['kind'], ['hero', 'side', 'angle']));
            if ($isGeneral && $kindMatch) {
                return '<img src="' . e(asset($img['path'])) . '" alt="' . e($img['alt'] ?: $alt) . '" loading="lazy" decoding="async" width="2000" height="2500">';
            }
        }
        
        $craftSlug = $pArr['craft'] ?? ($pArr['craft_slug'] ?? 'patina');
        if (is_array($craftSlug)) $craftSlug = $craftSlug['slug'] ?? 'patina';

        if ($kind === 'macro') {
            return macro_slot($craftSlug, $colour);
        }
        
        $drawing = is_array($pArr['drawing'] ?? null) ? $pArr['drawing'] : (json_decode($pArr['drawing_json'] ?? '{}', true) ?: []);
        return draw_slot([
            'shape' => $drawing['shape'] ?? 'loafer',
            'colour' => $colour,
            'craft' => $craftSlug,
            'art' => $drawing['art'] ?? null
        ], $alt);
    }
}

if (!function_exists('editorial_collections')) {
    function editorial_collections(): array
    {
        return Collection::editorial()->map(fn($item) => $item->toArray())->all();
    }
}

if (!function_exists('collection_by_slug')) {
    function collection_by_slug(string $slug): ?array
    {
        $c = Collection::where('slug', $slug)->where('active', true)->first();
        return $c ? $c->toArray() : null;
    }
}

if (!function_exists('active_categories')) {
    function active_categories(): array
    {
        try {
            return \App\Models\Category::where('active', true)->orderBy('sort')->orderBy('name')->get()->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
}

if (!function_exists('is_category_active')) {
    function is_category_active(string $catName): bool
    {
        try {
            $cat = \App\Models\Category::where('name', $catName)
                ->orWhere('slug', strtolower(trim($catName)))
                ->first();
            if ($cat) {
                return (bool) $cat->active;
            }
        } catch (\Throwable $e) {}
        return true;
    }
}

if (!function_exists('published_products')) {
    function published_products(): array
    {
        $inactiveCats = [];
        try {
            $inactiveCats = \App\Models\Category::where('active', false)->pluck('name')->all();
        } catch (\Throwable $e) {}

        $query = Product::with(['craft', 'colours', 'images', 'stock', 'collections'])
            ->whereIn('status', ['Published', 'Sold']);

        if (!empty($inactiveCats)) {
            $query->whereNotIn('category', $inactiveCats);
        }

        $products = $query->orderBy('featured', 'desc')
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        return $products->map(function ($p) {
            $arr = $p->toArray();
            $arr['colours'] = $p->colours->isEmpty()
                ? [['name' => 'Black', 'hex' => '#1f1c19', 'id' => null, 'price_diff' => 0]]
                : $p->colours->toArray();
            $arr['ready'] = $p->ready_sizes;
            $arr['images'] = $p->images->toArray();
            $arr['collections'] = $p->collections->pluck('slug')->all();
            $arr['craft'] = $p->craft_slug;
            $arr['craft_name'] = $p->craft_name;
            $arr['drawing'] = $p->drawing;
            $arr['occasion_list'] = $p->occasion_list;
            return $arr;
        })->all();
    }
}

if (!function_exists('product_by_slug')) {
    function product_by_slug(string $slug): ?array
    {
        $inactiveCats = [];
        try {
            $inactiveCats = \App\Models\Category::where('active', false)->pluck('name')->all();
        } catch (\Throwable $e) {}

        $query = Product::with(['craft', 'colours', 'images', 'stock', 'collections'])
            ->where('slug', $slug)
            ->whereIn('status', ['Published', 'Sold']);

        if (!empty($inactiveCats)) {
            $query->whereNotIn('category', $inactiveCats);
        }

        $p = $query->first();

        if (!$p) return null;

        $arr = $p->toArray();
        $arr['colours'] = $p->colours->isEmpty()
            ? [['name' => 'Black', 'hex' => '#1f1c19', 'id' => null, 'price_diff' => 0]]
            : $p->colours->toArray();
        $arr['ready'] = $p->ready_sizes;
        $arr['images'] = $p->images->toArray();
        $arr['collections'] = $p->collections->pluck('slug')->all();
        $arr['craft'] = $p->craft_slug;
        $arr['craft_name'] = $p->craft_name;
        $arr['drawing'] = $p->drawing;
        $arr['occasion_list'] = $p->occasion_list;
        return $arr;
    }
}

if (!function_exists('featured_products')) {
    function featured_products(int $n = 6): array
    {
        return array_slice(array_values(array_filter(published_products(), fn($p) => !empty($p['featured']))), 0, $n);
    }
}

if (!function_exists('product_url')) {
    function product_url(array $p): string
    {
        if (($p['category'] ?? '') === 'Service') {
            return url($p['slug']);
        }
        $cat = strtolower($p['category'] ?? 'men');
        $silSlug = Str::slug($p['silhouette'] ?? '') . 's';
        return url("shop/{$cat}/{$silSlug}/" . ($p['slug'] ?? ''));
    }
}

if (!function_exists('availability_label')) {
    function availability_label(array $p): string
    {
        return ($p['availability'] ?? '') === 'Made to Order'
            ? 'Made to Order, ' . ($p['lead_min'] ?? 5) . ' to ' . ($p['lead_max'] ?? 7) . ' weeks'
            : ($p['availability'] ?? '');
    }
}

if (!function_exists('size_options')) {
    function size_options(array $p): array
    {
        if (($p['size_type'] ?? '') === 'belt') return ['80 cm', '85 cm', '90 cm', '95 cm', '100 cm', '105 cm', '110 cm'];
        if (($p['size_type'] ?? '') === 'none') return [];
        $women = ($p['size_type'] ?? '') === 'shoe_women';
        $out = [];
        for ($n = $women ? 3 : 5; $n <= ($women ? 9 : 12); $n += 0.5) $out[] = 'UK ' . rtrim(rtrim(number_format($n, 1), '0'), '.');
        return $out;
    }
}

if (!function_exists('press_items')) {
    function press_items(): array
    {
        return PressItem::where('visible', true)->orderBy('sort')->orderBy('id')->get()->toArray();
    }
}

if (!function_exists('journal_posts')) {
    function journal_posts(string $status = 'Published'): array
    {
        return JournalPost::where('status', $status)->orderBy('published_at', 'desc')->orderBy('id')->get()->toArray();
    }
}

if (!function_exists('journal_by_slug')) {
    function journal_by_slug(string $slug): ?array
    {
        $j = JournalPost::where('slug', $slug)->where('status', 'Published')->first();
        return $j ? $j->toArray() : null;
    }
}

if (!function_exists('page_by_slug')) {
    function page_by_slug(string $slug): ?array
    {
        $p = Page::where('slug', $slug)->first();
        return $p ? $p->toArray() : null;
    }
}

if (!function_exists('content_blocks')) {
    function content_blocks(string $page = 'home'): array
    {
        return ContentBlock::forPage($page);
    }
}

if (!function_exists('search_site')) {
    function search_site(string $term): array
    {
        $t = mb_strtolower(trim($term));
        if ($t === '') return [];
        $syn = [
            'loafer' => ['slip-on', 'belgian', 'penny', 'loafer', 'mule'],
            'demule' => ['mule'],
            'whole cut' => ['wholecut'],
            'laser' => ['scarring'],
            'painted' => ['miniature'],
            'tattooed' => ['tattoo'],
            'wedding' => ['wedding', 'special occasion', 'peshawari'],
            'groom' => ['wedding', 'peshawari'],
            'sherwani' => ['wedding', 'peshawari'],
            'monogram' => ['initials', 'personalisation'],
            'initials' => ['personalisation', 'initials']
        ];
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
            $hay = $norm(implode(' ', [
                $p['name'], $p['silhouette'], $p['category'], $p['line'],
                $p['craft_name'], $p['occasions'], $p['poetic'],
                implode(' ', array_column($p['colours'], 'name')),
                implode(' ', $p['collections'])
            ]));
            if ($match($hay)) $res['Products'][] = ['t' => $p['name'] . ', ' . $p['silhouette'], 'h' => product_url($p)];
        }
        foreach (crafts() as $c) {
            if ($match($norm($c['name'] . ' ' . $c['tagline'] . ' ' . $c['description']))) {
                $res['Crafts'][] = ['t' => $c['name'] . ', ' . mb_strtolower($c['tagline']), 'h' => url('craft/' . $c['slug'])];
            }
        }
        foreach (journal_posts() as $j) {
            if ($match($norm($j['title'] . ' ' . $j['dek'] . ' ' . strip_tags((string) $j['body_html'])))) {
                $res['Journal'][] = ['t' => $j['title'], 'h' => url('journal/' . $j['slug'])];
            }
        }
        $pages = [
            ['Wedding', 'bespoke/wedding', 'wedding groom bride sherwani date'],
            ['Build your pair', 'bespoke/build', 'bespoke custom builder commission initials monogram personalisation colour'],
            ['Size guide', 'size-guide', 'size fit measure'],
            ['Care', 'care', 'care polish clean monsoon'],
            ['Restoration', 'restoration', 'restore resole repair'],
            ['Visit and appointments', 'visit', 'store kolkata lucknow appointment visit address'],
            ['Shipping', 'shipping', 'shipping duties delivery'],
            ['Returns and exchange', 'returns', 'returns exchange'],
            ['Our story', 'house/story', 'story founder rahul lakme history']
        ];
        foreach ($pages as [$title, $path, $k]) {
            if ($match($norm($title . ' ' . $k))) {
                $res['Pages'][] = ['t' => $title, 'h' => url($path)];
            }
        }
        return array_map(fn($a) => array_slice($a, 0, 6), $res);
    }
}
