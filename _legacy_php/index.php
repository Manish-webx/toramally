<?php
/**
 * Tōramally — front controller.
 * Every page request comes here (see .htaccess) and is sent to the right
 * template. To add a page: add a line to the routes below and create a
 * file in /templates/pages.
 */
require __DIR__ . '/app/bootstrap.php';

$path = trim(substr(strtok($_SERVER['REQUEST_URI'] ?? '/', '?'), strlen(base_path())), '/');
$path = rawurldecode($path);
$seg = $path === '' ? [] : explode('/', $path);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// ---------- API (forms, newsletter, search) ----------
if (($seg[0] ?? '') === 'api') {
    require APP . '/controllers/api.php';
    api_dispatch($seg[1] ?? '', $method);
    exit;
}

// ---------- Pages ----------
require APP . '/controllers/pages.php';

switch ($seg[0] ?? '') {
    case '':              page_home(); break;
    case 'shop':          page_shop($seg); break;
    case 'collection':    page_collection($seg[1] ?? ''); break;
    case 'craft':         isset($seg[1]) ? page_craft($seg[1]) : page_craft_index(); break;
    case 'bespoke':       page_bespoke($seg[1] ?? ''); break;
    case 'house':         page_house($seg[1] ?? ''); break;
    case 'journal':       isset($seg[1]) ? page_article(end($seg)) : page_journal(); break;
    case 'care':          page_simple('care', 'Care | Tōramally', 'How to care for patina, scarring, miniature, tattoo, carving and velvet pairs.'); break;
    case 'restoration':   page_simple('restoration', 'Restoration | Tōramally', 'Sole and heel replacement, re-patina, restoration and artwork restoration.'); break;
    case 'size-guide':    page_simple('size-guide', 'Size guide | Tōramally', 'Measure your foot and convert India, UK, EU and US sizes.'); break;
    case 'faq':           page_simple('faq', 'FAQ | Tōramally', 'Ordering, made to order, sizing, care, shipping, returns and weddings.'); break;
    case 'visit':
    case 'appointments':  page_simple('visit', 'Tōramally Kolkata: bespoke and handcrafted footwear', 'Visit the Tōramally flagship in Bhowanipore, Kolkata, or book a private appointment.'); break;
    case 'contact':       page_simple('contact', 'Contact | Tōramally', 'Write to the house, message us on WhatsApp or visit Kolkata.'); break;
    case 'received':      page_simple('received', 'Received | Tōramally', ''); break;
    case 'shipping':
    case 'returns':
    case 'privacy':
    case 'terms':
    case 'cookies':       page_policy($seg[0]); break;
    case 'search':        page_search($_GET['q'] ?? ''); break;
    case 'products':      // old Shopify addresses → new canonical product page
        $p = product_by_slug($seg[1] ?? '');
        $p ? redirect(product_url($p), 301) : page_404();
        break;
    default:
        // A product with its own top-level address, e.g. /shoe-shine-service
        $p = product_by_slug($seg[0]);
        ($p && count($seg) === 1 && $p['category'] === 'Service') ? page_product($p) : page_404();
}
