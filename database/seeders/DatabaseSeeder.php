<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Settings
        $settings = [
            ['k' => 'site_name', 'v' => 'Tōramally'],
            ['k' => 'site_url', 'v' => ''],
            ['k' => 'whatsapp', 'v' => ''],
            ['k' => 'phone', 'v' => ''],
            ['k' => 'email_public', 'v' => ''],
            ['k' => 'hours', 'v' => ''],
            ['k' => 'announcement', 'v' => ''],
            ['k' => 'address_store', 'v' => 'Ground Floor, 26 Chakraberia Road N, Jadubabur Bazar, Bhowanipore, Kolkata 700020'],
            ['k' => 'maps_url', 'v' => 'https://www.google.com/maps/search/?api=1&query=Toramally+26+Chakraberia+Road+North+Bhowanipore+Kolkata'],
            ['k' => 'instagram', 'v' => 'https://www.instagram.com/houseoftoramally/'],
            ['k' => 'facebook', 'v' => 'https://www.facebook.com/toramally/'],
            ['k' => 'business_name', 'v' => 'House of Tōramally'],
            ['k' => 'business_address', 'v' => '595-AA/050-A, Beside Olive Crown Hotel, 2nd Floor, Swaroop Chandra Khera, LDA Colony, Kanpur Road, Lucknow 226012'],
            ['k' => 'business_state', 'v' => 'Uttar Pradesh'],
            ['k' => 'business_state_code', 'v' => '09'],
            ['k' => 'gstin', 'v' => ''],
            ['k' => 'gst_rate_low', 'v' => '5'],
            ['k' => 'gst_rate_high', 'v' => '18'],
            ['k' => 'gst_threshold', 'v' => '2500'],
            ['k' => 'invoice_prefix', 'v' => 'TM'],
            ['k' => 'order_prefix', 'v' => 'TM-'],
            ['k' => 'commission_prefix', 'v' => 'CM-'],
            ['k' => 'currencies_json', 'v' => '{"INR": 1, "USD": 84, "GBP": 110, "EUR": 95, "AED": 23, "SGD": 64}'],
            ['k' => 'rounding_json', 'v' => '{"INR": 1, "USD": 5, "GBP": 5, "EUR": 5, "AED": 10, "SGD": 5}'],
            ['k' => 'commission_threshold', 'v' => '85000'],
            ['k' => 'patron_rate', 'v' => '5'],
            ['k' => 'ga_id', 'v' => ''],
            ['k' => 'meta_pixel_id', 'v' => ''],
            ['k' => 'seo_home_title', 'v' => 'Tōramally | Bootmaker. Hand painted, tattooed and carved shoes'],
            ['k' => 'seo_home_desc', 'v' => 'Hand painted, tattooed and carved shoes, made by hand in Lucknow. Flagship in Kolkata.'],
            ['k' => 'smtp_host', 'v' => ''],
            ['k' => 'smtp_port', 'v' => '465'],
            ['k' => 'smtp_secure', 'v' => 'ssl'],
            ['k' => 'smtp_user', 'v' => ''],
            ['k' => 'smtp_pass_enc', 'v' => ''],
            ['k' => 'mail_from', 'v' => ''],
            ['k' => 'mail_from_name', 'v' => 'Tōramally'],
            ['k' => 'orders_alert_email', 'v' => ''],
        ];
        foreach ($settings as $s) {
            DB::table('settings')->updateOrInsert(['k' => $s['k']], ['v' => $s['v'], 'created_at' => now(), 'updated_at' => now()]);
        }

        // Collections (Crafts & Collections)
        $collections = [
            ['id' => 1, 'slug' => 'velvet', 'type' => 'craft', 'name' => 'Velvet', 'scale_word' => 'Effortless', 'tagline' => 'Soft, everyday', 'description' => 'Velvet uppers for everyday ease, finished with a small leather patch, an all-over laser pattern, or a small hand-painted leather patch.', 'how_1' => 'Velvet is cut and lasted on a light, cemented construction.', 'how_2' => 'A calf patch is applied, lasered or painted by hand.', 'how_3' => 'A soft pair made for daily wear, not for occasion.', 'from_price' => 8000, 'lead_weeks' => '2 to 4', 'buy_mode_note' => 'Add to Bag', 'on_ladder' => 1, 'sort' => 10],
            ['id' => 2, 'slug' => 'patina', 'type' => 'craft', 'name' => 'Patina', 'scale_word' => 'Quiet', 'tagline' => 'Colour, by hand', 'description' => 'Instead of pre-coloured leather, colour is brushed onto calf one coat after another. The layers give a two-tone depth and the faint brush texture that is the house signature. The shine is built slowly with water and wax.', 'how_1' => 'Crust calf is lasted in its natural state.', 'how_2' => 'Dyes are brushed on by hand, layer over layer, heel and toe deepened.', 'how_3' => 'Water and wax are worked in by hand until the toe holds a mirror shine.', 'from_price' => 16000, 'lead_weeks' => '4 to 6', 'buy_mode_note' => 'Add to Bag', 'on_ladder' => 1, 'sort' => 20],
            ['id' => 3, 'slug' => 'scarring', 'type' => 'craft', 'name' => 'Scarring', 'scale_word' => 'Marked', 'tagline' => 'Marked by light', 'description' => 'Scarring (laser): the mark is made by removing, not adding. A beam lifts the surface of the calf in a precise pattern, which is then coloured and burnished so the design sits within the leather.', 'how_1' => 'The pattern is drawn and set to the size of your pair.', 'how_2' => 'The laser removes the finest layer of the grain.', 'how_3' => 'The marked leather is patinated and polished by hand.', 'from_price' => 24000, 'lead_weeks' => '5 to 7', 'buy_mode_note' => 'Add to Bag (House patterns); Commission (custom)', 'on_ladder' => 1, 'sort' => 30],
            ['id' => 4, 'slug' => 'miniature', 'type' => 'craft', 'name' => 'Miniature', 'scale_word' => 'Painted', 'tagline' => 'Art, painted by hand', 'description' => 'Miniature painting in the manner of the Lucknow and Mughal ateliers, applied directly to the finished shoe with fine brushes. Botanical borders, birds, the house fish. No two pairs are alike.', 'how_1' => 'The leather is prepared and the design pencilled onto the vamp.', 'how_2' => 'The artist paints in layers, the finest details with a single-hair brush.', 'how_3' => 'The painting is sealed and the pair is polished around it.', 'from_price' => 23000, 'lead_weeks' => '6 to 8', 'buy_mode_note' => 'Add to Bag (House artwork); Commission (custom)', 'on_ladder' => 1, 'sort' => 40],
            ['id' => 5, 'slug' => 'tattoo', 'type' => 'craft', 'name' => 'Tattoo', 'scale_word' => 'Expressive', 'tagline' => 'Drawn by hand', 'description' => 'Line work in the language of tattooing, worked into the leather by hand with a tattoo machine. Where Scarring is precise and geometric, Tattoo is the hand of the artist: ink, line and shading.', 'how_1' => 'The design is stencilled onto the upper.', 'how_2' => 'The artist inks the line work and shading by hand.', 'how_3' => 'The ink is set and the leather finished and polished.', 'from_price' => 28000, 'lead_weeks' => '6 to 8', 'buy_mode_note' => 'Add to Bag (House designs); Commission (custom)', 'on_ladder' => 1, 'sort' => 50],
            ['id' => 6, 'slug' => 'carving', 'type' => 'craft', 'name' => 'Carving', 'scale_word' => 'Sculpted', 'tagline' => 'Leather, sculpted by hand', 'description' => 'Relief carved and tooled into thick calf by hand. The most labour-intensive craft in the house: scales, paisleys and serpents raised from the surface, then coloured to deepen every cut.', 'how_1' => 'A thicker calf is chosen and the design transferred.', 'how_2' => 'Each line is cut, then bevelled and modelled to raise the relief.', 'how_3' => 'Colour is worked into the cuts and the surface burnished.', 'from_price' => 69000, 'lead_weeks' => '10 to 14', 'buy_mode_note' => 'Commission', 'on_ladder' => 1, 'sort' => 60],
            ['id' => 7, 'slug' => 'bespoke', 'type' => 'craft', 'name' => 'Bespoke', 'scale_word' => 'One of One', 'tagline' => 'Made around you', 'description' => 'Your silhouette, your colour, your artwork, your initials. A pair that exists once, made after a conversation, a design proof and your approval.', 'how_1' => 'We talk: occasion, fit, ideas, references.', 'how_2' => 'You approve a digital design proof and colour swatch.', 'how_3' => 'The workshop makes the pair and sends photographs as it takes shape.', 'from_price' => null, 'lead_weeks' => '8 to 14', 'buy_mode_note' => 'Commission', 'on_ladder' => 1, 'sort' => 70],
            ['id' => 8, 'slug' => 'classics', 'type' => 'collection', 'name' => 'Classics', 'scale_word' => null, 'tagline' => null, 'description' => "The house's permanent, signature designs.", 'how_1' => null, 'how_2' => null, 'how_3' => null, 'from_price' => null, 'lead_weeks' => null, 'buy_mode_note' => null, 'on_ladder' => 0, 'sort' => 80],
            ['id' => 9, 'slug' => 'tattooed', 'type' => 'collection', 'name' => 'Tattooed', 'scale_word' => null, 'tagline' => null, 'description' => 'Pairs with tattoo line work drawn by hand.', 'how_1' => null, 'how_2' => null, 'how_3' => null, 'from_price' => null, 'lead_weeks' => null, 'buy_mode_note' => null, 'on_ladder' => 0, 'sort' => 90],
            ['id' => 10, 'slug' => 'hand-painted', 'type' => 'collection', 'name' => 'Hand Painted', 'scale_word' => null, 'tagline' => null, 'description' => 'Patina and miniature, painted by hand.', 'how_1' => null, 'how_2' => null, 'how_3' => null, 'from_price' => null, 'lead_weeks' => null, 'buy_mode_note' => null, 'on_ladder' => 0, 'sort' => 100],
            ['id' => 11, 'slug' => 'carved', 'type' => 'collection', 'name' => 'Carved', 'scale_word' => null, 'tagline' => null, 'description' => 'Relief carved into leather by hand.', 'how_1' => null, 'how_2' => null, 'how_3' => null, 'from_price' => null, 'lead_weeks' => null, 'buy_mode_note' => null, 'on_ladder' => 0, 'sort' => 110],
            ['id' => 12, 'slug' => 'miniatures', 'type' => 'collection', 'name' => 'Miniatures', 'scale_word' => null, 'tagline' => null, 'description' => 'Miniature painting in the Lucknow manner.', 'how_1' => null, 'how_2' => null, 'how_3' => null, 'from_price' => null, 'lead_weeks' => null, 'buy_mode_note' => null, 'on_ladder' => 0, 'sort' => 120],
        ];
        foreach ($collections as $c) {
            DB::table('collections')->updateOrInsert(['id' => $c['id']], array_merge($c, ['active' => 1, 'created_at' => now(), 'updated_at' => now()]));
        }

        // Products
        $products = [
            ['id' => 1, 'slug' => 'taus', 'name' => 'Taus', 'poetic' => 'A peacock in miniature, painted across the vamp.', 'story' => null, 'category' => 'Men', 'line' => 'Special Occasion', 'silhouette' => 'Belgian loafer', 'last_name' => 'LOBO', 'craft_id' => 4, 'personalisation_level' => 'House Design', 'construction' => 'Blake stitched', 'material' => 'Calf', 'base_price' => 23000, 'availability' => 'Made to Order', 'lead_min' => 5, 'lead_max' => 7, 'buy_mode' => 'Add to Bag', 'occasions' => 'Wedding,Evening', 'size_type' => 'shoe_men', 'featured' => 1, 'drawing_json' => '{"shape": "loafer", "art": "peacock"}', 'hsn_code' => '6403', 'sort' => 10],
            ['id' => 2, 'slug' => 'noor', 'name' => 'Noor', 'poetic' => 'A backless Belgian, burnished to a quiet glow.', 'story' => null, 'category' => 'Men', 'line' => 'Classic', 'silhouette' => 'Mule', 'last_name' => 'LOBO', 'craft_id' => 2, 'personalisation_level' => 'House Design', 'construction' => 'Blake stitched', 'material' => 'Calf', 'base_price' => 16000, 'availability' => 'Ready to Ship', 'lead_min' => 5, 'lead_max' => 7, 'buy_mode' => 'Add to Bag', 'occasions' => 'Everyday', 'size_type' => 'shoe_men', 'featured' => 1, 'drawing_json' => '{"shape": "mule"}', 'hsn_code' => '6403', 'sort' => 20],
            ['id' => 3, 'slug' => 'shaam', 'name' => 'Shaam', 'poetic' => 'Evening colour, layered by hand.', 'story' => null, 'category' => 'Men', 'line' => 'Classic', 'silhouette' => 'Belgian loafer', 'last_name' => 'LOBO', 'craft_id' => 2, 'personalisation_level' => 'House Design', 'construction' => 'Blake stitched', 'material' => 'Calf', 'base_price' => 18000, 'availability' => 'Ready to Ship', 'lead_min' => 5, 'lead_max' => 7, 'buy_mode' => 'Add to Bag', 'occasions' => 'Everyday,Evening', 'size_type' => 'shoe_men', 'featured' => 1, 'drawing_json' => '{"shape": "loafer"}', 'hsn_code' => '6403', 'sort' => 30],
            ['id' => 4, 'slug' => 'adab', 'name' => 'Adab', 'poetic' => 'The Oxford, as the house first imagined it.', 'story' => null, 'category' => 'Men', 'line' => 'Classic', 'silhouette' => 'Oxford', 'last_name' => 'RAY', 'craft_id' => 2, 'personalisation_level' => 'House Design', 'construction' => 'Goodyear welted', 'material' => 'Calf', 'base_price' => 26000, 'availability' => 'Made to Order', 'lead_min' => 5, 'lead_max' => 7, 'buy_mode' => 'Add to Bag', 'occasions' => 'Everyday,Evening', 'size_type' => 'shoe_men', 'featured' => 1, 'drawing_json' => '{"shape": "oxford"}', 'hsn_code' => '6403', 'sort' => 40],
            ['id' => 5, 'slug' => 'chaman', 'name' => 'Chaman', 'poetic' => 'A garden painted on a single piece of calf.', 'story' => null, 'category' => 'Men', 'line' => 'Special Occasion', 'silhouette' => 'Wholecut Oxford', 'last_name' => 'Aziz', 'craft_id' => 4, 'personalisation_level' => 'House Design', 'construction' => 'Goodyear welted', 'material' => 'Calf', 'base_price' => 33000, 'availability' => 'Made to Order', 'lead_min' => 6, 'lead_max' => 8, 'buy_mode' => 'Add to Bag', 'occasions' => 'Wedding,Statement', 'size_type' => 'shoe_men', 'featured' => 1, 'drawing_json' => '{"shape": "wholecut", "art": "botanical"}', 'hsn_code' => '6403', 'sort' => 50],
            ['id' => 6, 'slug' => 'naqsh', 'name' => 'Naqsh', 'poetic' => 'A lattice lifted from the grain by light.', 'story' => null, 'category' => 'Men', 'line' => 'Classic', 'silhouette' => 'Penny loafer', 'last_name' => '031', 'craft_id' => 3, 'personalisation_level' => 'House Design', 'construction' => 'Blake stitched', 'material' => 'Calf', 'base_price' => 28000, 'availability' => 'Made to Order', 'lead_min' => 5, 'lead_max' => 7, 'buy_mode' => 'Add to Bag', 'occasions' => 'Everyday,Statement', 'size_type' => 'shoe_men', 'featured' => 0, 'drawing_json' => '{"shape": "penny"}', 'hsn_code' => '6403', 'sort' => 60],
            ['id' => 7, 'slug' => 'siyahi', 'name' => 'Siyahi', 'poetic' => 'Ink line work across the quarter, drawn by hand.', 'story' => null, 'category' => 'Men', 'line' => 'Special Occasion', 'silhouette' => 'Derby', 'last_name' => '054', 'craft_id' => 5, 'personalisation_level' => 'House Design', 'construction' => 'Goodyear welted', 'material' => 'Calf', 'base_price' => 32000, 'availability' => 'Made to Order', 'lead_min' => 6, 'lead_max' => 8, 'buy_mode' => 'Add to Bag', 'occasions' => 'Statement,Evening', 'size_type' => 'shoe_men', 'featured' => 0, 'drawing_json' => '{"shape": "derby", "art": "mandala"}', 'hsn_code' => '6403', 'sort' => 70],
            ['id' => 8, 'slug' => 'nag', 'name' => 'Nāg', 'poetic' => 'A serpent raised from the leather, scale by scale.', 'story' => null, 'category' => 'Men', 'line' => 'Special Occasion', 'silhouette' => 'Adelaide Oxford', 'last_name' => '714', 'craft_id' => 6, 'personalisation_level' => 'One of One', 'construction' => 'Goodyear welted', 'material' => 'Calf', 'base_price' => 69000, 'availability' => 'Commission', 'lead_min' => 10, 'lead_max' => 14, 'buy_mode' => 'Commission', 'occasions' => 'Statement', 'size_type' => 'shoe_men', 'featured' => 0, 'drawing_json' => '{"shape": "adelaide", "art": "snake"}', 'hsn_code' => '6403', 'sort' => 80],
            ['id' => 9, 'slug' => 'darbar', 'name' => 'Darbar', 'poetic' => 'A court of flowers and birds, painted end to end.', 'story' => null, 'category' => 'Men', 'line' => 'Special Occasion', 'silhouette' => 'Belgian loafer', 'last_name' => 'LOBO', 'craft_id' => 4, 'personalisation_level' => 'Bespoke', 'construction' => 'Blake stitched', 'material' => 'Calf', 'base_price' => 85000, 'availability' => 'Commission', 'lead_min' => 10, 'lead_max' => 12, 'buy_mode' => 'Commission', 'occasions' => 'Wedding', 'size_type' => 'shoe_men', 'featured' => 0, 'drawing_json' => '{"shape": "loafer", "art": "botanical"}', 'hsn_code' => '6403', 'sort' => 90],
            ['id' => 10, 'slug' => 'rukhsat', 'name' => 'Rukhsat', 'poetic' => 'For the groom, the Peshawari in burnished tan.', 'story' => null, 'category' => 'Men', 'line' => 'Special Occasion', 'silhouette' => 'Peshawari', 'last_name' => 'LOBO', 'craft_id' => 2, 'personalisation_level' => 'House Design', 'construction' => 'Blake stitched', 'material' => 'Calf', 'base_price' => 20000, 'availability' => 'Made to Order', 'lead_min' => 4, 'lead_max' => 6, 'buy_mode' => 'Add to Bag', 'occasions' => 'Wedding', 'size_type' => 'shoe_men', 'featured' => 0, 'drawing_json' => '{"shape": "mule"}', 'hsn_code' => '6403', 'sort' => 100],
            ['id' => 11, 'slug' => 'gulnar', 'name' => 'Gulnār', 'poetic' => 'A kitten heel with a pomegranate bloom.', 'story' => null, 'category' => 'Women', 'line' => '', 'silhouette' => 'Heel', 'last_name' => null, 'craft_id' => 4, 'personalisation_level' => 'House Design', 'construction' => 'Cemented', 'material' => 'Calf', 'base_price' => 24000, 'availability' => 'Made to Order', 'lead_min' => 5, 'lead_max' => 7, 'buy_mode' => 'Add to Bag', 'occasions' => 'Wedding,Evening', 'size_type' => 'shoe_women', 'featured' => 0, 'drawing_json' => '{"shape": "heel", "art": "botanical"}', 'hsn_code' => '6403', 'sort' => 110],
            ['id' => 12, 'slug' => 'mehr', 'name' => 'Mehr', 'poetic' => 'A flat mule marked in a fine lattice.', 'story' => null, 'category' => 'Women', 'line' => '', 'silhouette' => 'Mule', 'last_name' => null, 'craft_id' => 3, 'personalisation_level' => 'House Design', 'construction' => 'Cemented', 'material' => 'Calf', 'base_price' => 18000, 'availability' => 'Ready to Ship', 'lead_min' => 4, 'lead_max' => 6, 'buy_mode' => 'Add to Bag', 'occasions' => 'Everyday', 'size_type' => 'shoe_women', 'featured' => 0, 'drawing_json' => '{"shape": "mule"}', 'hsn_code' => '6403', 'sort' => 120],
            ['id' => 13, 'slug' => 'zoya', 'name' => 'Zoya', 'poetic' => 'A soft flat, painted at the toe.', 'story' => null, 'category' => 'Women', 'line' => '', 'silhouette' => 'Flat', 'last_name' => null, 'craft_id' => 4, 'personalisation_level' => 'House Design', 'construction' => 'Cemented', 'material' => 'Calf', 'base_price' => 16500, 'availability' => 'Made to Order', 'lead_min' => 4, 'lead_max' => 6, 'buy_mode' => 'Add to Bag', 'occasions' => 'Everyday,Wedding', 'size_type' => 'shoe_women', 'featured' => 0, 'drawing_json' => '{"shape": "flat", "art": "botanical"}', 'hsn_code' => '6403', 'sort' => 130],
            ['id' => 14, 'slug' => 'makhmal', 'name' => 'Makhmal', 'poetic' => 'Velvet for every day, with a small leather patch.', 'story' => null, 'category' => 'Everyday', 'line' => '', 'silhouette' => 'Loafer', 'last_name' => null, 'craft_id' => 1, 'personalisation_level' => 'House Design', 'construction' => 'Cemented', 'material' => 'Velvet', 'base_price' => 8500, 'availability' => 'Ready to Ship', 'lead_min' => 2, 'lead_max' => 4, 'buy_mode' => 'Add to Bag', 'occasions' => 'Everyday', 'size_type' => 'shoe_men', 'featured' => 1, 'drawing_json' => '{"shape": "slipper"}', 'hsn_code' => '6403', 'sort' => 140],
            ['id' => 15, 'slug' => 'aaram', 'name' => 'Ārām', 'poetic' => 'A house slipper, as it should be.', 'story' => null, 'category' => 'Everyday', 'line' => '', 'silhouette' => 'Slipper', 'last_name' => null, 'craft_id' => 1, 'personalisation_level' => 'House Design', 'construction' => 'Cemented', 'material' => 'Velvet', 'base_price' => 5500, 'availability' => 'Ready to Ship', 'lead_min' => 2, 'lead_max' => 3, 'buy_mode' => 'Add to Bag', 'occasions' => 'Everyday', 'size_type' => 'shoe_men', 'featured' => 0, 'drawing_json' => '{"shape": "slipper"}', 'hsn_code' => '6403', 'sort' => 150],
            ['id' => 16, 'slug' => 'kamar', 'name' => 'Kamar', 'poetic' => 'A patina belt to match your pair.', 'story' => null, 'category' => 'Accessories', 'line' => '', 'silhouette' => 'Belt', 'last_name' => null, 'craft_id' => 2, 'personalisation_level' => 'Personalised', 'construction' => 'Hand stitched', 'material' => 'Calf', 'base_price' => 9500, 'availability' => 'Made to Order', 'lead_min' => 3, 'lead_max' => 4, 'buy_mode' => 'Add to Bag', 'occasions' => 'Everyday', 'size_type' => 'belt', 'featured' => 0, 'drawing_json' => '{"shape": "belt"}', 'hsn_code' => '4203', 'sort' => 160],
            ['id' => 17, 'slug' => 'batua', 'name' => 'Batua', 'poetic' => 'A billfold with tattoo line work on the face.', 'story' => null, 'category' => 'Accessories', 'line' => '', 'silhouette' => 'Wallet', 'last_name' => null, 'craft_id' => 5, 'personalisation_level' => 'House Design', 'construction' => 'Hand stitched', 'material' => 'Calf', 'base_price' => 7500, 'availability' => 'Ready to Ship', 'lead_min' => 3, 'lead_max' => 4, 'buy_mode' => 'Add to Bag', 'occasions' => 'Everyday', 'size_type' => 'none', 'featured' => 0, 'drawing_json' => '{"shape": "wallet", "art": "mandala"}', 'hsn_code' => '4202', 'sort' => 170],
            ['id' => 18, 'slug' => 'pebble', 'name' => 'Tōramally Pebble', 'poetic' => 'A small painted keepsake from the workshop.', 'story' => null, 'category' => 'Accessories', 'line' => '', 'silhouette' => 'Collectible', 'last_name' => null, 'craft_id' => 4, 'personalisation_level' => 'House Design', 'construction' => null, 'material' => 'Calf', 'base_price' => 4500, 'availability' => 'Ready to Ship', 'lead_min' => 2, 'lead_max' => 3, 'buy_mode' => 'Add to Bag', 'occasions' => 'Everyday', 'size_type' => 'none', 'featured' => 0, 'drawing_json' => '{"shape": "pebble", "art": "mahi"}', 'hsn_code' => '9701', 'sort' => 180],
            ['id' => 19, 'slug' => 'shoe-shine-service', 'name' => 'The Shoe Shine Service', 'poetic' => 'Cleaned, fed and polished by hand, and returned.', 'story' => 'Cleaning, conditioning, fresh laces where needed, and a hand-built polish to the toe and heel. For any brand of good leather shoes. Send your pairs to the Kolkata house or bring them in. We confirm by WhatsApp when they are ready.', 'category' => 'Service', 'line' => '', 'silhouette' => 'Service', 'last_name' => null, 'craft_id' => 2, 'personalisation_level' => 'House Design', 'construction' => null, 'material' => 'Calf', 'base_price' => 1500, 'availability' => 'Ready to Ship', 'lead_min' => 1, 'lead_max' => 2, 'buy_mode' => 'Add to Bag', 'occasions' => '', 'size_type' => 'none', 'featured' => 0, 'drawing_json' => '{"shape": "loafer"}', 'hsn_code' => '9997', 'sort' => 190],
        ];
        foreach ($products as $p) {
            DB::table('products')->updateOrInsert(['id' => $p['id']], array_merge($p, ['status' => 'Published', 'created_at' => now(), 'updated_at' => now()]));
        }

        // Product Colours
        $colours = [
            [1, 1, 'Oxblood', '#4b1719', 0], [2, 1, 'Black', '#1f1c19', 1], [3, 1, 'Navy', '#1e2b45', 2],
            [4, 2, 'Cognac', '#8f4b21', 0], [5, 2, 'Tobacco', '#6a4528', 1], [6, 2, 'Black', '#1f1c19', 2],
            [7, 3, 'Burgundy', '#5e1f2c', 0], [8, 3, 'Oxblood', '#4b1719', 1], [9, 3, 'Forest Green', '#26412f', 2], [10, 3, 'Navy', '#1e2b45', 3],
            [11, 4, 'Black', '#1f1c19', 0], [12, 4, 'Dark Brown', '#3c2619', 1], [13, 4, 'Oxblood', '#4b1719', 2],
            [14, 5, 'Tobacco', '#6a4528', 0], [15, 5, 'Ivory', '#e9e0cc', 1], [16, 5, 'Black', '#1f1c19', 2],
            [17, 6, 'Dark Brown', '#3c2619', 0], [18, 6, 'Black', '#1f1c19', 1], [19, 6, 'Cognac', '#8f4b21', 2],
            [20, 7, 'Tan', '#a66e3d', 0], [21, 7, 'Cognac', '#8f4b21', 1], [22, 7, 'Beige', '#c9b28f', 2],
            [23, 8, 'Cognac', '#8f4b21', 0], [24, 8, 'Dark Brown', '#3c2619', 1],
            [25, 9, 'Ivory', '#e9e0cc', 0], [26, 9, 'Beige', '#c9b28f', 1],
            [27, 10, 'Tan', '#a66e3d', 0], [28, 10, 'Cognac', '#8f4b21', 1], [29, 10, 'Ivory', '#e9e0cc', 2],
            [30, 11, 'Ivory', '#e9e0cc', 0], [31, 11, 'Black', '#1f1c19', 1], [32, 11, 'Burgundy', '#5e1f2c', 2],
            [33, 12, 'Black', '#1f1c19', 0], [34, 12, 'Tan', '#a66e3d', 1], [35, 12, 'Beige', '#c9b28f', 2],
            [36, 13, 'Beige', '#c9b28f', 0], [37, 13, 'Ivory', '#e9e0cc', 1], [38, 13, 'Navy', '#1e2b45', 2],
            [39, 14, 'Forest Green', '#26412f', 0], [40, 14, 'Navy', '#1e2b45', 1], [41, 14, 'Burgundy', '#5e1f2c', 2], [42, 14, 'Black', '#1f1c19', 3],
            [43, 15, 'Navy', '#1e2b45', 0], [44, 15, 'Burgundy', '#5e1f2c', 1],
            [45, 16, 'Tan', '#a66e3d', 0], [46, 16, 'Dark Brown', '#3c2619', 1], [47, 16, 'Black', '#1f1c19', 2], [48, 16, 'Oxblood', '#4b1719', 3],
            [49, 17, 'Tan', '#a66e3d', 0], [50, 17, 'Cognac', '#8f4b21', 1], [51, 17, 'Black', '#1f1c19', 2],
            [52, 18, 'Oxblood', '#4b1719', 0], [53, 18, 'Forest Green', '#26412f', 1], [54, 18, 'Ivory', '#e9e0cc', 2],
            [55, 19, 'Black', '#1f1c19', 0],
        ];
        foreach ($colours as [$id, $pid, $name, $hex, $sort]) {
            DB::table('product_colours')->updateOrInsert(['id' => $id], [
                'product_id' => $pid, 'name' => $name, 'hex' => $hex, 'price_diff' => 0, 'sort' => $sort
            ]);
        }

        // Product Stock
        $stocks = [
            [2, null, 'UK 8', 2], [2, null, 'UK 9', 2], [2, null, 'UK 10', 2],
            [3, null, 'UK 7', 2], [3, null, 'UK 8', 2], [3, null, 'UK 9', 2],
            [12, null, 'UK 4', 2], [12, null, 'UK 5', 2], [12, null, 'UK 6', 2],
            [14, null, 'UK 7', 2], [14, null, 'UK 8', 2], [14, null, 'UK 9', 2], [14, null, 'UK 10', 2],
            [15, null, 'UK 7', 2], [15, null, 'UK 8', 2], [15, null, 'UK 9', 2], [15, null, 'UK 10', 2], [15, null, 'UK 11', 2],
            [17, null, 'One size', 2],
            [18, null, 'One size', 2],
        ];
        foreach ($stocks as [$pid, $cid, $size, $qty]) {
            DB::table('product_stock')->updateOrInsert(['product_id' => $pid, 'colour_id' => $cid, 'size' => $size], ['qty' => $qty]);
        }

        // Product Collection links
        $prodCols = [
            [1, 10], [1, 12], [1, 4],
            [2, 8], [2, 10], [2, 2],
            [3, 8], [3, 10], [3, 2],
            [4, 8], [4, 10], [4, 2],
            [5, 10], [5, 12], [5, 4],
            [6, 8], [6, 3],
            [7, 9], [7, 5],
            [8, 11], [8, 6],
            [9, 10], [9, 12], [9, 4],
            [10, 10], [10, 2],
            [11, 10], [11, 12], [11, 4],
            [12, 3],
            [13, 10], [13, 12], [13, 4],
            [14, 1],
            [15, 1],
            [16, 10], [16, 2],
            [17, 9], [17, 5],
            [18, 10], [18, 12], [18, 4],
            [19, 10], [19, 2],
        ];
        foreach ($prodCols as [$pid, $cid]) {
            DB::table('product_collection')->updateOrInsert(['product_id' => $pid, 'collection_id' => $cid]);
        }

        // Press items
        $press = [
            ['Lakmé Fashion Week', 'The house debuted at Summer/Resort 2018 with hand-welted shoes, belts and wallets.', 'event', 10],
            ['The Shoe Snob', 'Reviewed the hand-welted construction and the tattooed, hand-painted and carved finishes.', 'press', 20],
            ['Shoes & Accessories', 'Profiled the house as a pioneer of tattooed and hand-painted leather footwear in India.', 'press', 30],
            ['Aashni + Co', 'Stockist. Hand-painted mules, loafers and occasion footwear.', 'stockist', 40],
            ['Aza Fashions', "Stockist. Men's occasion and classic footwear.", 'stockist', 50],
            ['Tata CLiQ Luxury', 'Stockist. Designer leather shoes and loafers.', 'stockist', 60],
        ];
        foreach ($press as [$name, $note, $kind, $sort]) {
            DB::table('press_items')->updateOrInsert(['name' => $name], ['note' => $note, 'kind' => $kind, 'sort' => $sort, 'visible' => 1, 'created_at' => now(), 'updated_at' => now()]);
        }

        // Journal Posts
        $journal = [
            ['what-is-scarring', 'Craft', 'What is scarring?', 'A mark made by taking away, not adding.', '<p>Most decoration on a shoe is added: paint, stitching, a buckle. Scarring works the other way round. A beam of light lifts the finest layer of the grain, and the pattern that remains is part of the leather itself.</p><p>In the workshop the design is drawn first, then scaled to the size of your pair so that the lattice meets the seams where it should. After the laser comes the part that makes it a Tōramally: the marked calf is patinated by hand, so colour settles deeper into the scarred lines and the pattern reads in two tones.</p><p>Scarring is often confused with our Tattoo craft. They are different. Scarring is precise and geometric, made by light. Tattoo is drawn by hand, line by line, with ink.</p><p>Care is simple: a neutral cream, a soft brush, and a patient polish. Avoid heavy coloured polish, which can fill the finer lines.</p>', 3, 'Published', now()],
            ['the-art-of-patina', 'Craft', 'The art of patina', 'Colour, one coat after another.', '<p>Rather than cutting a shoe from leather that arrives already coloured, we begin with calf in its natural state and bring the colour by hand.</p><p>Dye is brushed on in thin layers. Heel and toe are taken deeper than the waist, which gives the pair its shadow and shape. The brush leaves a faint texture that we keep on purpose. It is the signature of a hand at work.</p><p>Then comes the shine. Water and wax are worked in by hand, layer upon layer, until the toe holds a mirror. It takes hours, and it cannot be hurried.</p><p>Because the colour is built rather than printed, a patina pair can be taken darker later. Bring it to the house and we can deepen it for you.</p>', 2, 'Published', now()],
            ['why-handmade-shoes-take-weeks', 'Stories', 'Why handmade shoes take weeks', 'Time is part of the process.', '<p>A made-to-order Tōramally usually takes five to seven weeks. Painted and tattooed pairs take a little longer, carved pairs longer still.</p><p>The time is spent where you can see it. The upper is lasted and left to take its shape. Welted pairs are stitched by hand to the welt and then to the sole. The waist is shaped. Colour is layered and allowed to rest between coats. A miniature painting is built up over days.</p><p>We would rather tell you honestly how long a pair takes than hurry it. When a date matters, such as a wedding, tell us early and we will plan the making around it.</p>', 4, 'Published', now()],
            ['choosing-wedding-shoes-for-the-groom', 'Style', 'Choosing wedding shoes for the groom', 'Begin with the date, then the silhouette.', '<p>Start with time. A made-to-order pair needs five to eight weeks; a commission with custom artwork needs ten or more. Our wedding date check will tell you what is possible.</p><p>Then the outfit. A sherwani or achkan sits well with a Peshawari, a Belgian loafer or a mule. A bandhgala suit can take a wholecut Oxford.</p><p>Finally, how much of yourselves goes into the pair. Initials, the wedding date inside the heel, a family motif, or a miniature painted to match the embroidery. Matching belts and pairs for the family can be made at the same time.</p>', 4, 'Published', now()],
            ['what-is-a-belgian-loafer', 'Craft', 'What is a Belgian loafer?', null, null, null, 'In preparation', null],
            ['what-is-leather-tattooing', 'Craft', 'What is leather tattooing?', null, null, null, 'In preparation', null],
            ['from-miniature-painting-to-leather', 'Craft', 'From miniature painting to leather', null, null, null, 'In preparation', null],
            ['why-no-two-hand-painted-shoes-are-alike', 'Craft', 'Why no two hand-painted shoes are alike', null, null, null, 'In preparation', null],
            ['the-anatomy-of-a-toramally-shoe', 'Craft', 'The anatomy of a Tōramally shoe', null, null, null, 'In preparation', null],
            ['how-to-care-for-patina-leather', 'Craft', 'How to care for patina leather', null, null, null, 'In preparation', null],
        ];
        foreach ($journal as [$slug, $category, $title, $dek, $body, $craftId, $status, $pub]) {
            DB::table('journal_posts')->updateOrInsert(['slug' => $slug], [
                'category' => $category, 'title' => $title, 'dek' => $dek, 'body_html' => $body,
                'craft_id' => $craftId, 'status' => $status, 'published_at' => $pub,
                'created_at' => now(), 'updated_at' => now()
            ]);
        }

        // Pages
        $pages = [
            ['privacy', 'Privacy Policy', "<p>We collect only what you give us: your name and contact details when you create an account, enquire, book or order, and the details of your pair. We use them to make and deliver your order, to reply to you, and, if you agree, to send Letters from the House.</p><p>We do not sell your data. Card and UPI details are entered on our payment partner's secure page and never reach our servers. You can ask us to see, correct or delete your data at any time by writing to us.</p><p>We follow India's Digital Personal Data Protection Act, 2023 and, for visitors from Europe, the GDPR.</p>"],
            ['terms', 'Terms & Conditions', '<p>Prices are set in INR and shown in other currencies from a fixed table reviewed monthly. An order is confirmed when payment is received and the house confirms it. Estimated prices for custom work become final when confirmed in writing.</p><p>Made-to-order and commissioned pairs are made individually; lead times are estimates and we will keep you informed.</p>'],
            ['shipping', 'Shipping Policy', '<p>Domestic delivery across India and international delivery by insured courier. Ready-to-ship pairs leave the house within a few working days; made-to-order pairs ship when finished.</p><p>International clients pay import duties on delivery; we share an estimate before you confirm. Tracking is sent by email and is visible in My Account.</p>'],
            ['returns', 'Return & Exchange Policy', '<p>Ready-to-ship pairs, unworn and in the box, can be exchanged for size within 7 days of delivery. Personalised, made-to-order and commissioned pairs are made for you and are not returnable. If there is a fault in the making, we remake the pair.</p>'],
            ['cookies', 'Cookie Policy', '<p>We store your bag, currency and builder progress in your browser. Analytics and advertising tags load only if you accept them.</p>'],
        ];
        foreach ($pages as [$slug, $title, $body]) {
            DB::table('pages')->updateOrInsert(['slug' => $slug], ['title' => $title, 'body_html' => $body, 'created_at' => now(), 'updated_at' => now()]);
        }

        // Content Blocks
        $blocks = [
            ['home', 'hero', '{"title": "Crafted in silence.", "text": "Hand painted, tattooed and carved shoes, made by hand in our Lucknow workshop.", "cta1": "Discover the collection", "cta2": "Commission your pair", "caption": "Taus, Belgian loafer. Miniature, painted by hand.", "product": "taus"}', 10],
            ['home', 'house', '{"title": "A house that happens to make shoes.", "text": "Tōramally joins Lucknow\'s refinement to Kolkata\'s artistic spirit. Our shoes are made in a small workshop, cut from calf, coloured by hand, and finished with painting, tattoo, scarring or carving by the artisans who own each craft.\\n\\nThe house began with hand-welted shoes, belts and wallets at Lakmé Fashion Week in 2018, and was among the first in India to tattoo and hand-paint leather footwear."}', 20],
            ['home', 'ladder', '{"title": "The craft ladder", "text": "Seven levels of hand. Each step asks a little more of the artisan, and gives a little more of you to the pair."}', 30],
            ['home', 'shop', '{"title": "Shop"}', 40],
            ['home', 'signature', '{"title": "Signature pairs", "text": "Ready to ship, or made to order in weeks."}', 50],
            ['home', 'bespoke', '{"title": "Made for you.", "text": "You do not simply choose a Tōramally. You decide how much of yourself goes into it."}', 60],
            ['home', 'wedding', '{"title": "Made for the day you remember.", "text": "Pairs for the groom, the bride, and the family. Initials, the date inside the heel, a family motif, or a miniature painted to match the embroidery."}', 70],
            ['home', 'press', '{"title": "Press and stockists"}', 80],
            ['home', 'making', '{"title": "The making", "text": "The macro is the proof. Brush, beam, needle and blade."}', 90],
            ['home', 'visit', '{"title": "Visit the house in Kolkata."}', 100],
        ];
        foreach ($blocks as [$page, $blockKey, $data, $sort]) {
            DB::table('content_blocks')->updateOrInsert(['page' => $page, 'block_key' => $blockKey], ['data_json' => $data, 'sort' => $sort, 'visible' => 1, 'created_at' => now(), 'updated_at' => now()]);
        }

        // Payment Gateways
        $gateways = [
            ['razorpay', 'Razorpay', 'domestic', 'UPI, cards, netbanking and wallets', 10],
            ['payu', 'PayU', 'domestic', 'UPI, cards and netbanking', 20],
            ['ccavenue', 'CCAvenue', 'both', 'Cards, UPI and netbanking', 30],
            ['stripe', 'Stripe', 'international', 'International cards', 40],
            ['paypal', 'PayPal', 'international', 'PayPal and international cards', 50],
        ];
        foreach ($gateways as [$code, $name, $region, $label, $sort]) {
            DB::table('payment_gateways')->updateOrInsert(['code' => $code], ['name' => $name, 'region' => $region, 'display_label' => $label, 'sort' => $sort, 'created_at' => now(), 'updated_at' => now()]);
        }

        // Shipping Rates
        $shipping = [
            ['domestic', 'Insured delivery within India', 0, null, '3 to 6 working days after dispatch', 10],
            ['international', 'Insured international courier', 3500, null, '5 to 10 working days after dispatch', 20],
        ];
        foreach ($shipping as [$scope, $name, $rate, $freeAbove, $eta, $sort]) {
            DB::table('shipping_rates')->updateOrInsert(['scope' => $scope, 'name' => $name], ['rate_inr' => $rate, 'free_above_inr' => $freeAbove, 'eta_text' => $eta, 'sort' => $sort, 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}
