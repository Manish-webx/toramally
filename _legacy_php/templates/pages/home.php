<?php
/**
 * Home. Sections come from content_blocks (Admin > Content > Homepage):
 * each can be reordered, hidden, or have its text changed.
 * Vars: $blocks, $featured, $press
 */
$para = fn($t) => implode('', array_map(fn($x) => '<p>' . e($x) . '</p>', array_filter(preg_split("/\n\s*\n/", (string) $t))));
foreach ($blocks as $key => $b):
switch ($key):

case 'hero':
  $hp = product_by_slug($b['product'] ?? 'taus'); ?>
  <section class="hero dark">
    <div class="wrap">
      <div>
        <h1 class="fade-in"><?= e($b['title'] ?? 'Crafted in silence.') ?></h1>
        <p class="sub"><?= e($b['text'] ?? '') ?></p>
        <div class="row"><a class="btn" href="<?= url('shop') ?>"><?= e($b['cta1'] ?? 'Discover the collection') ?></a><a class="btn ghost" href="<?= url('bespoke/build') ?>"><?= e($b['cta2'] ?? 'Commission your pair') ?></a></div>
      </div>
      <div class="hero-art draw-anim"><div class="plate" aria-hidden="true"></div>
        <?php if ($hp && $hp['images']): echo product_visual($hp); else: ?>
        <div class="draw" data-draw='{"shape":"loafer","colour":"Oxblood","craft":"miniature","art":"peacock","artScale":1.12}' role="img" aria-label="Taus, an oxblood Belgian loafer with a peacock painted by hand across the vamp"></div>
        <?php endif; ?>
      </div>
    </div>
    <?php if ($hp): ?><a class="note" href="<?= product_url($hp) ?>" style="text-decoration:none"><?= e($b['caption'] ?? '') ?></a><?php endif; ?>
  </section>
<?php break;

case 'house': ?>
  <section class="section"><div class="wrap split">
    <div class="art"><?= draw_slot(['shape' => 'wholecut', 'colour' => 'Tobacco', 'craft' => 'patina'], 'A tobacco wholecut Oxford with hand-layered patina') ?></div>
    <div><h2 class="h2"><?= e($b['title']) ?></h2><div style="margin-top:20px"><?= $para($b['text'] ?? '') ?></div><a class="tlink" href="<?= url('house/story') ?>">Discover Tōramally</a></div>
  </div></section>
<?php break;

case 'ladder': ?>
  <section class="section dark"><div class="wrap">
    <div class="head"><div><h2 class="h2"><?= e($b['title']) ?></h2><p class="muted" style="margin-top:8px"><?= e($b['text'] ?? '') ?></p></div><a class="tlink" href="<?= url('craft') ?>">All crafts</a></div>
    <?php partial('ladder'); ?>
  </div></section>
<?php break;

case 'shop': ?>
  <section class="section"><div class="wrap">
    <div class="head"><h2 class="h2"><?= e($b['title'] ?? 'Shop') ?></h2><a class="tlink" href="<?= url('shop') ?>">All pairs</a></div>
    <div class="grid g4">
      <?php foreach ([['Men', 'shop/men', 'loafer', 'Oxblood', 'miniature', 'peacock'], ['Women', 'shop/women', 'heel', 'Ivory', 'miniature', 'botanical'], ['Accessories', 'shop/accessories', 'belt', 'Tan', 'patina', null], ['Everyday', 'shop/everyday', 'slipper', 'Forest Green', 'velvet', null]] as [$t, $h, $s, $c, $cr, $a]): ?>
        <a class="tile" href="<?= url($h) ?>"><div class="img"><?= draw_slot(['shape' => $s, 'colour' => $c, 'craft' => $cr, 'art' => $a], $t) ?></div><h3 class="h3"><?= e($t) ?></h3></a>
      <?php endforeach; ?>
    </div>
  </div></section>
<?php break;

case 'signature': ?>
  <section class="section panel"><div class="wrap">
    <div class="head"><div><h2 class="h2"><?= e($b['title']) ?></h2><p style="margin-top:8px"><?= e($b['text'] ?? '') ?></p></div><a class="tlink" href="<?= url('shop/men/classic') ?>">Men, Classic</a></div>
    <div class="grid g3"><?php foreach ($featured as $p) partial('product-card', ['p' => $p]); ?></div>
  </div></section>
<?php break;

case 'bespoke': ?>
  <section class="section dark"><div class="wrap"><div class="split">
    <div>
      <h2 class="h2"><?= e($b['title']) ?></h2>
      <p class="muted" style="margin:16px 0 32px"><?= e($b['text'] ?? '') ?></p>
      <ol class="steps4">
        <li><h3 class="h3">Silhouette</h3><p class="muted small">Loafer, Oxford, mule, belt or wallet.</p></li>
        <li><h3 class="h3">Colour</h3><p class="muted small">Eleven house colours, or your own.</p></li>
        <li><h3 class="h3">Craft</h3><p class="muted small">Patina, scarring, miniature, tattoo, carving.</p></li>
        <li><h3 class="h3">Your mark</h3><p class="muted small">Initials, gold, brass nails, your artwork.</p></li>
      </ol>
      <div class="row" style="margin-top:32px"><a class="btn" href="<?= url('bespoke/build') ?>" data-track="begin_commission">Begin your commission</a><a class="btn ghost" href="<?= url('appointments') ?>">Book an appointment</a></div>
    </div>
    <div class="art" style="background:#27463a"><?= draw_slot(['shape' => 'loafer', 'colour' => 'Black', 'craft' => 'bespoke', 'art' => 'mahi', 'initials' => 'R K', 'gold' => true, 'nails' => true], 'A black Belgian loafer with the house fish painted in gold and gold initials on the heel') ?></div>
  </div></div></section>
<?php break;

case 'wedding': ?>
  <section class="section"><div class="wrap split">
    <div><h2 class="h2"><?= e($b['title']) ?></h2><p style="margin-top:16px"><?= e($b['text'] ?? '') ?></p>
      <p class="small muted">Tell us your date and we will tell you, now, what can be made in time.</p>
      <div class="row" style="margin-top:8px"><a class="btn" href="<?= url('bespoke/wedding') ?>">Plan your wedding pairs</a></div></div>
    <div class="art wide"><?= draw_slot(['shape' => 'mule', 'colour' => 'Ivory', 'craft' => 'miniature', 'art' => 'botanical'], 'An ivory Peshawari-style mule with a botanical miniature painting') ?></div>
  </div></section>
<?php break;

case 'press': ?>
  <section class="section panel"><div class="wrap">
    <div class="head"><h2 class="h2"><?= e($b['title']) ?></h2><a class="tlink" href="<?= url('house/press') ?>">More</a></div>
    <div class="press"><?php foreach ($press as $x): ?><div><b><?= e($x['name']) ?></b><span class="small muted"><?= e($x['note']) ?></span></div><?php endforeach; ?></div>
  </div></section>
<?php break;

case 'making': ?>
  <section class="section"><div class="wrap">
    <div class="head"><div><h2 class="h2"><?= e($b['title']) ?></h2><p style="margin-top:8px"><?= e($b['text'] ?? '') ?></p></div><a class="tlink" href="<?= url('house/workshop') ?>">Inside the workshop</a></div>
    <div class="grid g3"><?php foreach (['patina', 'scarring', 'carving'] as $k): $c = craft_by_slug($k); if (!$c) continue; ?>
      <a class="tile" href="<?= url('craft/' . $k) ?>"><div class="img" style="aspect-ratio:1"><?= macro_slot($k, craft_colour($k)) ?></div><h3 class="h3"><?= e($c['name']) ?></h3><p class="small muted"><?= e($c['tagline']) ?></p></a>
    <?php endforeach; ?></div>
  </div></section>
<?php break;

case 'visit': ?>
  <section class="section dark"><div class="wrap split">
    <div>
      <h2 class="h2"><?= e($b['title']) ?></h2>
      <p class="muted" style="margin-top:16px"><?= e(setting('address_store')) ?></p>
      <p class="muted small"><?= e(setting('hours', "Call or message us for today's hours. Private appointments available.")) ?></p>
      <div class="row"><a class="btn" href="<?= url('appointments') ?>">Book an appointment</a><a class="btn ghost" href="<?= e(setting('maps_url')) ?>" target="_blank" rel="noopener">Open in Maps</a></div>
    </div>
    <div>
      <h3 class="h3">@houseoftoramally</h3>
      <p class="muted small" style="margin-top:6px">New pairs, commissions and work in progress, every week.</p>
      <div class="grid g3" style="gap:8px;margin:16px 0"><?php foreach ([['miniature', 'Ivory'], ['tattoo', 'Beige'], ['patina', 'Oxblood']] as [$k, $col]): ?>
        <a href="<?= e(setting('instagram')) ?>" target="_blank" rel="noopener" style="aspect-ratio:1;display:block;overflow:hidden" aria-label="Tōramally on Instagram"><?= macro_slot($k, $col) ?></a>
      <?php endforeach; ?></div>
      <a class="tlink" href="<?= e(setting('instagram')) ?>" target="_blank" rel="noopener">Follow on Instagram</a>
    </div>
  </div></section>
<?php break;
endswitch;
endforeach;
