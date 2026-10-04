<?php
/** One template for every craft. Vars: $c (craft), $pairs */
$all = crafts(); $i = craft_index($c['slug']); $prev = $all[$i - 1] ?? null; $next = $all[$i + 1] ?? null;
$title = $c['slug'] === 'scarring' ? 'Scarring (laser)' : $c['name'];
$buildCraft = in_array($c['slug'], ['velvet', 'bespoke'], true) ? '' : '?craft=' . $c['slug'];
?>
<section class="dark"><div class="wrap split" style="padding-top:40px;padding-bottom:64px">
  <div><?php partial('crumbs', ['items' => [['Home', ''], ['Craft', 'craft'], [$c['name'], null]], 'light' => true]); ?>
    <h1 class="h1" style="margin-top:24px"><?= e($title) ?></h1><p class="serif-i" style="font-size:26px;color:#cdb47f;margin:8px 0 20px"><?= e($c['tagline']) ?></p>
    <p class="muted"><?= e($c['description']) ?></p>
    <p class="small muted"><?= $c['from_price'] ? 'From ' . money((int) $c['from_price']) . '.' : 'Priced after consultation.' ?> Lead time <?= e($c['lead_weeks']) ?> weeks. <?= e($c['buy_mode_note']) ?>.</p>
    <div class="row" style="margin-top:12px"><a class="btn" href="<?= url('bespoke/build' . $buildCraft) ?>"><?= $c['slug'] === 'velvet' ? 'Build a pair' : 'Customise in ' . e($c['name']) ?></a><?php if ($pairs): ?><a class="btn ghost" href="<?= url('shop?craft=' . $c['slug']) ?>">Shop <?= e($c['name']) ?></a><?php endif; ?></div>
  </div>
  <div class="art" style="aspect-ratio:1"><?= macro_slot($c['slug'], craft_colour($c['slug'])) ?></div>
</div></section>
<section class="section"><div class="wrap">
  <div class="head"><h2 class="h2">How it is made</h2><?php partial('ladder-indicator', ['slug' => $c['slug']]); ?></div>
  <ol class="steps4" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
    <?php foreach ([['The material', $c['how_1']], ['The hand', $c['how_2']], ['The result', $c['how_3']]] as [$h, $t]): ?><li><h3 class="h3"><?= $h ?></h3><p class="small" style="margin-top:6px"><?= e($t) ?></p></li><?php endforeach; ?>
  </ol>
  <?php if ($c['slug'] === 'scarring'): ?>
  <div class="grid g2" style="margin-top:48px">
    <figure style="margin:0"><div class="art" style="aspect-ratio:1"><?= macro_slot('patina', 'Dark Brown', 'Patinated calf before scarring') ?></div><figcaption class="small muted" style="margin-top:8px">Before: patinated calf.</figcaption></figure>
    <figure style="margin:0"><div class="art" style="aspect-ratio:1"><?= macro_slot('scarring', 'Dark Brown', 'The same calf after scarring') ?></div><figcaption class="small muted" style="margin-top:8px">After: the lattice, lifted by light.</figcaption></figure>
  </div>
  <?php endif; ?>
</div></section>
<?php if ($pairs): ?>
<section class="section panel"><div class="wrap"><div class="head"><h2 class="h2">Pairs in <?= e($c['name']) ?></h2><a class="tlink" href="<?= url('shop?craft=' . $c['slug']) ?>">All</a></div>
  <div class="grid g3"><?php foreach ($pairs as $p) partial('product-card', ['p' => $p]); ?></div></div></section>
<?php endif; ?>
<section class="section" style="padding-top:48px"><div class="wrap row" style="justify-content:space-between;border-top:1px solid var(--line);padding-top:24px">
  <?= $prev ? '<a class="tlink" href="' . url('craft/' . $prev['slug']) . '">Step back: ' . e($prev['name']) . '</a>' : '<span></span>' ?>
  <a class="tlink" href="<?= url('care') ?>"><?= e($c['name']) ?> care</a>
  <?= $next ? '<a class="tlink" href="' . url('craft/' . $next['slug']) . '">Step up: ' . e($next['name']) . '</a>' : '<span></span>' ?>
</div></section>
