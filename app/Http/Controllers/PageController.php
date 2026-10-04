<?php

namespace App\Http\Controllers;

use App\Models\Celebrity;
use App\Models\Page;
use App\Models\Product;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function home()
    {
        $blocks = content_blocks('home');
        $featured = featured_products(6);
        $press = press_items();

        return view('pages.home', [
            'blocks' => $blocks,
            'featured' => $featured,
            'press' => $press,
            'meta' => [
                'title' => setting('seo_home_title', 'Tōramally | Bootmaker. Hand painted, tattooed and carved shoes'),
                'description' => setting('seo_home_desc', 'Hand painted, tattooed and carved shoes, made by hand in Lucknow. Flagship in Kolkata.'),
                'canonical' => url('/'),
                'nav' => 'home',
            ]
        ]);
    }

    public function shopFilterDefs(): array
    {
        $bands = [
            ['Under 10k', 0, 9999],
            ['10k to 20k', 10000, 19999],
            ['20k to 35k', 20000, 34999],
            ['35k to 60k', 35000, 59999],
            ['60k and above', 60000, PHP_INT_MAX]
        ];

        return [
            'craft'      => ['Craft', array_map(fn($c) => [$c['slug'], $c['name']], array_filter(crafts(), fn($c) => $c['slug'] !== 'bespoke')), fn($p, $v) => $p['craft'] === $v],
            'collection' => ['Collection', array_map(fn($c) => [$c['slug'], $c['name']], editorial_collections()), fn($p, $v) => in_array($v, $p['collections'] ?? [], true)],
            'sil'        => ['Silhouette', null, fn($p, $v) => $p['silhouette'] === $v],
            'price'      => ['Price (INR)', array_map(fn($b) => [$b[0], $b[0]], $bands), function ($p, $v) use ($bands) {
                foreach ($bands as $b) {
                    if ($b[0] === $v) return $p['base_price'] >= $b[1] && $p['base_price'] <= $b[2];
                }
                return false;
            }],
            'avail'      => ['Availability', [['Ready to Ship', 'Ready to Ship'], ['Made to Order', 'Made to Order'], ['Commission', 'Commission']], fn($p, $v) => $p['availability'] === $v],
            'colour'     => ['Colour', null, fn($p, $v) => in_array($v, array_column($p['colours'] ?? [], 'name'), true)],
            'occasion'   => ['Occasion', [['Everyday', 'Everyday'], ['Wedding', 'Wedding'], ['Evening', 'Evening'], ['Statement', 'Statement']], fn($p, $v) => in_array($v, $p['occasion_list'] ?? [], true)],
            'size'       => ['Size', array_map(fn($s) => [$s, $s], ['UK 4', 'UK 5', 'UK 6', 'UK 7', 'UK 8', 'UK 9', 'UK 10', 'UK 11']), fn($p, $v) => ($p['size_type'] ?? '') !== 'none' && (($p['availability'] ?? '') !== 'Ready to Ship' || in_array($v, $p['ready'] ?? [], true))],
        ];
    }

    public function shop(Request $request, ?string $catKey = null, ?string $sub = null, ?string $productSlug = null)
    {
        $cats = [];
        try {
            $cats = \App\Models\Category::where('active', true)->pluck('name', 'slug')->all();
        } catch (\Throwable $e) {}

        if (empty($cats) && \App\Models\Category::count() === 0) {
            $cats = ['men' => 'Men', 'women' => 'Women', 'accessories' => 'Accessories', 'everyday' => 'Everyday'];
        }
        
        if ($catKey !== null && $catKey !== '') {
            $catKeySlug = strtolower(trim($catKey));
            if (!isset($cats[$catKeySlug])) {
                abort(404);
            }
            $cat = $cats[$catKeySlug];
        } else {
            $cat = null;
        }

        // /shop/{cat}/{silhouette}/{product} → product page
        if ($productSlug !== null) {
            $p = product_by_slug($productSlug);
            if (!$p || \Illuminate\Support\Str::slug($p['category']) !== \Illuminate\Support\Str::slug($catKey)) {
                abort(404);
            }
            $canonical = product_url($p);
            if (url($request->path()) !== $canonical) {
                return redirect($canonical, 301);
            }
            return $this->productDetail($p);
        }

        $line = null;
        $silFromPath = null;
        if ($sub !== null && $sub !== '') {
            if ($cat === 'Men' && $sub === 'classic') {
                $line = 'Classic';
            } elseif ($cat === 'Men' && $sub === 'special-occasion') {
                $line = 'Special Occasion';
            } else {
                $silFromPath = $sub;
            }
        }

        $pool = array_values(array_filter(published_products(), function ($p) use ($cat, $line) {
            return ($p['category'] ?? '') !== 'Service' && (!$cat || ($p['category'] ?? '') === $cat) && (!$line || ($p['line'] ?? '') === $line);
        }));

        if ($silFromPath) {
            $match = array_filter($pool, fn($p) => (Str::slug($p['silhouette']) . 's') === $silFromPath);
            if (!$match) {
                abort(404);
            }
            $request->merge(['sil' => reset($match)['silhouette']]);
        }

        $defs = $this->shopFilterDefs();
        $defs['sil'][1] = array_map(fn($s) => [$s, $s], array_values(array_unique(array_column($pool, 'silhouette'))));
        $colours = [];
        foreach ($pool as $p) {
            foreach ($p['colours'] ?? [] as $c) {
                $colours[$c['name']] = $c['hex'];
            }
        }
        $defs['colour'][1] = array_map(fn($n) => [$n, $n], array_keys($colours));

        $sel = [];
        foreach ($defs as $k => $_) {
            $val = (string) ($request->query($k, ''));
            $sel[$k] = array_values(array_filter(explode('|', $val)));
        }

        $apply = function (array $list, ?string $skip = null) use ($defs, $sel) {
            return array_values(array_filter($list, function ($p) use ($defs, $sel, $skip) {
                foreach ($defs as $k => [, , $fn]) {
                    if ($k === $skip || empty($sel[$k])) continue;
                    $ok = false;
                    foreach ($sel[$k] as $v) {
                        if ($fn($p, $v)) {
                            $ok = true;
                            break;
                        }
                    }
                    if (!$ok) return false;
                }
                return true;
            }));
        };

        $results = $apply($pool);
        $sort = $request->query('sort', 'featured');
        usort($results, match ($sort) {
            'low' => fn($a, $b) => $a['base_price'] <=> $b['base_price'],
            'high' => fn($a, $b) => $b['base_price'] <=> $a['base_price'],
            'new' => fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? '') ?: ($b['id'] <=> $a['id']),
            default => fn($a, $b) => [($b['featured'] ?? 0), ($a['sort'] ?? 0)] <=> [($a['featured'] ?? 0), ($b['sort'] ?? 0)],
        });

        $counts = [];
        foreach ($defs as $k => [, $opts, $fn]) {
            $base = $apply($pool, $k);
            foreach ($opts as [$v]) {
                $counts[$k][$v] = count(array_filter($base, fn($p) => $fn($p, $v)));
            }
        }

        $title = $cat ? ($line ? "$cat, $line" : $cat) : 'All pairs';
        $subDesc = [
            'Men' => 'Classic signature designs, and pairs for the occasions that matter.',
            'Women' => 'Heels, flats and mules, painted and marked by hand.',
            'Accessories' => 'Belts, wallets and collectibles in the house finishes.',
            'Everyday' => 'Velvet and slippers. Soft pairs for every day.'
        ][$cat] ?? 'Every pair in the house, ready to ship or made to order.';

        return view('pages.shop', [
            'cat' => $cat,
            'catKey' => $catKey,
            'line' => $line,
            'pool' => $pool,
            'results' => $results,
            'defs' => $defs,
            'sel' => $sel,
            'counts' => $counts,
            'sort' => $sort,
            'title' => $title,
            'sub' => $subDesc,
            'colours' => $colours,
            'meta' => [
                'title' => $title . ' | Handcrafted in India | Tōramally',
                'description' => $subDesc,
                'canonical' => url($request->path()),
                'nav' => 'shop',
            ]
        ]);
    }

    public function collection(string $slug)
    {
        $c = collection_by_slug($slug);
        if (!$c) {
            abort(404);
        }
        if (($c['type'] ?? '') === 'craft') {
            return redirect('craft/' . $slug, 301);
        }
        return redirect('shop?collection=' . rawurlencode($slug));
    }

    public function productDetail(array $p)
    {
        $related = array_slice(array_values(array_filter(published_products(), function ($x) use ($p) {
            return ($x['category'] ?? '') !== 'Service' && $x['id'] !== $p['id'] && (($x['craft'] ?? '') === ($p['craft'] ?? '') || ($x['silhouette'] ?? '') === ($p['silhouette'] ?? ''));
        })), 0, 4);

        $craft = craft_by_slug($p['craft'] ?? 'patina');
        $ld = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => 'Tōramally ' . $p['name'],
            'description' => $p['poetic'] ?? '',
            'brand' => ['@type' => 'Brand', 'name' => 'Tōramally'],
            'sku' => $p['slug'],
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'INR',
                'price' => $p['base_price'],
                'url' => product_url($p),
                'availability' => ($p['status'] ?? '') === 'Sold'
                    ? 'https://schema.org/SoldOut'
                    : (($p['availability'] ?? '') === 'Ready to Ship' ? 'https://schema.org/InStock' : 'https://schema.org/PreOrder')
            ]
        ];

        return view('pages.product', [
            'p' => $p,
            'related' => $related,
            'craft' => $craft,
            'meta' => [
                'title' => ($p['seo_title'] ?? '') ?: ($p['name'] . ': ' . $p['silhouette'] . ', ' . ($p['craft_name'] ?? '') . ' | Tōramally'),
                'description' => ($p['seo_desc'] ?? '') ?: mb_substr(($p['poetic'] ?? '') . ' ' . (!empty($p['construction']) ? $p['construction'] . '. ' : '') . 'Handcrafted in India.', 0, 155),
                'canonical' => product_url($p),
                'json_ld' => $ld,
                'nav' => 'shop',
                'body_class' => 'is-product',
            ]
        ]);
    }

    public function craftIndex()
    {
        return view('pages.craft-index', [
            'meta' => [
                'title' => 'The craft ladder | Tōramally',
                'description' => 'Velvet, Patina, Scarring, Miniature, Tattoo, Carving and Bespoke. Seven levels of hand.',
                'nav' => 'craft',
            ]
        ]);
    }

    public function craft(string $slug)
    {
        $c = craft_by_slug($slug);
        if (!$c) {
            abort(404);
        }
        $pairs = array_values(array_filter(published_products(), fn($p) => ($p['category'] ?? '') !== 'Service' && ($p['craft'] ?? '') === $slug));

        return view('pages.craft', [
            'c' => $c,
            'pairs' => $pairs,
            'meta' => [
                'title' => ($c['seo_title'] ?? '') ?: ($c['name'] . ': ' . ($c['tagline'] ?? '') . ' | Tōramally'),
                'description' => ($c['seo_desc'] ?? '') ?: mb_substr((string) ($c['description'] ?? ''), 0, 155),
                'nav' => 'craft',
            ]
        ]);
    }

    public function bespoke(Request $request, ?string $sub = null)
    {
        $sub = (string) ($sub ?? '');
        switch ($sub) {
            case '':
                return view('pages.bespoke', [
                    'meta' => [
                        'title' => 'Bespoke | Made for you | Tōramally',
                        'description' => 'Build your pair: silhouette, colour, craft, artwork and initials, with a live estimate.',
                        'nav' => 'bespoke',
                    ]
                ]);
            case 'build':
                $from = product_by_slug((string) $request->query('from', ''));
                return view('pages.builder', [
                    'from' => $from,
                    'meta' => [
                        'title' => 'Build your pair | Tōramally',
                        'description' => 'Choose a silhouette, colour, craft, artwork and initials, and see the estimate as you go.',
                        'nav' => 'bespoke',
                    ]
                ]);
            case 'wedding':
                return view('pages.wedding', [
                    'meta' => [
                        'title' => 'Wedding shoes for the groom and bride | Tōramally',
                        'description' => 'Pairs for the groom, the bride and the family. Check whether your wedding date can be met.',
                        'nav' => 'bespoke',
                    ]
                ]);
            case 'one-of-one':
                return view('pages.enquiry', [
                    'kind' => 'oneofone',
                    'meta' => [
                        'title' => 'One of One | Tōramally',
                        'description' => 'Exceptional and collector pieces, made once.',
                        'nav' => 'bespoke',
                    ]
                ]);
            case 'designers':
                return view('pages.enquiry', [
                    'kind' => 'designers',
                    'meta' => [
                        'title' => 'Designers & Collectors | Tōramally',
                        'description' => 'For designers, retailers, stylists, film, corporate gifting and collaborations.',
                        'nav' => 'bespoke',
                    ]
                ]);
            case 'custom-colour':
            case 'personalisation':
            case 'custom-artwork':
                return view('pages.bespoke', [
                    'scrollTo' => $sub,
                    'meta' => [
                        'title' => 'Bespoke | Tōramally',
                        'nav' => 'bespoke',
                    ]
                ]);
        }
        abort(404);
    }

    public function house(Request $request, ?string $sub = null)
    {
        $sub = (string) ($sub ?? '');
        if ($sub === 'worn-by') {
            $people = Celebrity::where('visible', true)->where('consent_on_file', true)->orderBy('sort')->orderBy('id')->get()->toArray();
            return view('pages.worn-by', [
                'people' => $people,
                'meta' => [
                    'title' => 'Worn by | Tōramally',
                    'description' => 'Clients and friends of the house, in their Tōramally pairs.',
                    'nav' => 'house',
                ]
            ]);
        }
        if ($sub === 'videos') {
            $videos = Video::where('visible', true)->orderBy('sort')->orderBy('id')->get()->toArray();
            return view('pages.videos', [
                'videos' => $videos,
                'meta' => [
                    'title' => 'Videos | Tōramally',
                    'description' => 'Films of the making: brush, beam, needle and blade.',
                    'nav' => 'house',
                ]
            ]);
        }
        $sections = ['', 'story', 'lucknow', 'kolkata', 'workshop', 'artisans', 'philosophy', 'materials', 'press', 'stockists'];
        if (!in_array($sub, $sections, true)) {
            abort(404);
        }
        return view('pages.house', [
            'scrollTo' => $sub,
            'press' => press_items(),
            'meta' => [
                'title' => 'The House | Lucknow and Kolkata | Tōramally',
                'description' => 'Our story, our workshop in Lucknow and our flagship in Kolkata.',
                'nav' => 'house',
                'canonical' => url('/house'),
            ]
        ]);
    }

    public function journal()
    {
        return view('pages.journal', [
            'posts' => journal_posts('Published'),
            'soon' => journal_posts('In preparation'),
            'meta' => [
                'title' => 'Journal | Tōramally',
                'description' => 'Notes on craft, style, materials, people and places.',
                'nav' => 'journal',
            ]
        ]);
    }

    public function article(string $slug)
    {
        $a = journal_by_slug($slug);
        if (!$a) {
            abort(404);
        }
        $craft = craft_by_id((int) ($a['craft_id'] ?? 0));
        $related = $craft ? array_slice(array_values(array_filter(published_products(), fn($p) => ($p['craft'] ?? '') === $craft['slug'])), 0, 3) : [];

        return view('pages.article', [
            'a' => $a,
            'craft' => $craft,
            'related' => $related,
            'meta' => [
                'title' => (($a['seo_title'] ?? '') ?: $a['title']) . ' | Tōramally Journal',
                'description' => ($a['seo_desc'] ?? '') ?: ($a['dek'] ?? ''),
                'nav' => 'journal',
                'json_ld' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => $a['title'],
                    'datePublished' => $a['published_at'] ?? null,
                    'publisher' => ['@type' => 'Organization', 'name' => 'Tōramally']
                ]
            ]
        ]);
    }

    public function policy(string $slug)
    {
        $pg = page_by_slug($slug);
        if (!$pg) {
            abort(404);
        }
        return view('pages.policy', [
            'pg' => $pg,
            'meta' => [
                'title' => (($pg['seo_title'] ?? '') ?: $pg['title']) . ' | Tōramally',
                'description' => $pg['seo_desc'] ?? '',
            ]
        ]);
    }

    public function simple(string $tpl)
    {
        $titles = [
            'care' => ['Care | Tōramally', 'How to care for patina, scarring, miniature, tattoo, carving and velvet pairs.'],
            'restoration' => ['Restoration | Tōramally', 'Sole and heel replacement, re-patina, restoration and artwork restoration.'],
            'size-guide' => ['Size guide | Tōramally', 'Measure your foot and convert India, UK, EU and US sizes.'],
            'faq' => ['FAQ | Tōramally', 'Ordering, made to order, sizing, care, shipping, returns and weddings.'],
            'visit' => ['Tōramally Kolkata: bespoke and handcrafted footwear', 'Visit the Tōramally flagship in Bhowanipore, Kolkata, or book a private appointment.'],
            'contact' => ['Contact | Tōramally', 'Write to the house, message us on WhatsApp or visit Kolkata.'],
            'received' => ['Received | Tōramally', ''],
        ];

        if (!isset($titles[$tpl])) {
            abort(404);
        }

        [$title, $desc] = $titles[$tpl];
        return view('pages.' . $tpl, [
            'meta' => [
                'title' => $title,
                'description' => $desc,
                'nav' => in_array($tpl, ['visit', 'contact'], true) ? 'house' : '',
            ]
        ]);
    }

    public function search(Request $request)
    {
        $q = (string) $request->query('q', '');
        return view('pages.search', [
            'q' => $q,
            'results' => search_site($q),
            'meta' => [
                'title' => 'Search | Tōramally',
                'description' => '',
            ]
        ]);
    }

    public function directProduct(string $slug)
    {
        $p = product_by_slug($slug);
        if ($p && ($p['category'] ?? '') === 'Service') {
            return $this->productDetail($p);
        }
        abort(404);
    }
}
