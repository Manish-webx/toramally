<?php
/** The House: story, Lucknow, Kolkata, workshop, philosophy, materials, press. Vars: $press */
$sec = function (string $id, string $title, string $body, string $art, bool $left) { ?>
  <section class="section" id="<?= $id ?>"><div class="wrap split">
    <?php if ($left): ?><div class="art"><?= $art ?></div><?php endif; ?>
    <div><h2 class="h2"><?= e($title) ?></h2><div style="margin-top:16px"><?= $body ?></div></div>
    <?php if (!$left): ?><div class="art"><?= $art ?></div><?php endif; ?>
  </div></section>
<?php };
?>
<div class="wrap"><?php partial('crumbs', ['items' => [['Home', ''], ['The House', null]]]); ?>
  <div style="margin-top:12px"><?php partial('page-head', ['title' => 'The House', 'sub' => "Lucknow's memory, Kolkata's spirit, and a small workshop between them."]); ?></div></div>
<?php
$sec('story', 'Our story', '<p>Tōramally was founded by Rahul, who grew up in Uttar Pradesh among one of India\'s oldest traditions of leathercraft, including the rare practice of inking leather. The founding intention was simple: a house that expresses India\'s own artistic legacy through the made-to-order shoe, rather than borrowing someone else\'s.</p><p>The house launched in February 2018 at Lakmé Fashion Week Summer/Resort with hand-welted shoes, belts and wallets, and was among the first in India to tattoo and hand-paint leather footwear. It has grown largely through Instagram and word of mouth, and many of its clients once travelled abroad to find shoes of this kind.</p>', draw_slot(['shape' => 'oxford', 'colour' => 'Black', 'craft' => 'patina'], 'A black patina Oxford'), false);
$sec('lucknow', 'From Lucknow, with memory.', '<p>Miniature painting, Nawabi ornament, the architecture of arches and screens, and a culture of poetry and refinement. Our miniature and tattoo work begins here, and so does our emblem: a fish drawn from the royal Mahi-Maratib insignia.</p><p>The workshop is in Lucknow. Visits are by appointment.</p>', '<div style="width:100%;height:100%;display:grid;place-items:center;background:var(--green-900)"><div style="width:60%"><span data-emblem data-emblem-light></span></div></div>', true);
$sec('kolkata', 'From the city of culture.', '<p>Art, literature, theatre, architecture and fashion. Our flagship is in Bhowanipore, where you can see every craft on the ladder, be measured, and begin a commission.</p><p class="small muted">' . e(setting('address_store')) . '</p><a class="tlink" href="' . url('visit') . '">Visit and appointments</a>', draw_slot(['shape' => 'mule', 'colour' => 'Cognac', 'craft' => 'patina'], 'A cognac patina mule'), false);
$sec('workshop', 'A workshop, not a factory.', '<p>A small team of artisans, each with a craft of their own: lasting and welting, patina, painting, tattooing, carving. Our Goodyear-welted pairs carry a fiddle waist, shaped by hand beneath the arch, and a Y-shaped bevel on the sole.</p><p>Colour is never bought in. Each pair is painted coat after coat, which gives the two-tone depth and faint brush texture that mark a Tōramally, and then polished with water and wax, over hours, to a mirror shine.</p>', macro_slot('patina', 'Oxblood'), true);
$sec('philosophy', 'Philosophy', '<p>Slow luxury. Timeless over seasonal. Handcrafted, with the small imperfections that give a pair its soul. Made to be kept, and when needed, restored. A patina pair can be taken darker years later; bring it back to the house.</p>', macro_slot('scarring', 'Dark Brown'), false);
$sec('materials', 'Materials', '<p>Calfskin is the house leather. Velvet appears only in the everyday range. Brass for nails and hardware. Every pair is presented in a wooden box, lined in deep green, with botanical motifs.</p>', '<div style="width:100%;height:100%;display:grid;place-items:center"><div data-box style="width:70%"></div></div>', true);
?>
<section class="section panel" id="press"><div class="wrap"><div class="head"><h2 class="h2">Press and stockists</h2></div>
  <div class="press"><?php foreach ($press as $x): ?><div><b><?= $x['url'] ? '<a href="' . e($x['url']) . '" target="_blank" rel="noopener" style="text-decoration:none">' . e($x['name']) . '</a>' : e($x['name']) ?></b><span class="small muted"><?= e($x['note']) ?></span></div><?php endforeach; ?></div>
  <p class="small muted" style="margin-top:24px" id="stockists">Partner stockists in Hyderabad and Bangalore. Message us for the nearest one.</p>
  <div class="row" style="margin-top:16px"><a class="tlink" href="<?= url('house/worn-by') ?>">Worn by</a><a class="tlink" href="<?= url('house/videos') ?>">Videos</a></div>
</div></section>
