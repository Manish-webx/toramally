<?php
/* Product card. The whole card links straight to the product page (no intermediate pages).
   Vars: $p (hydrated product) */
$c1 = $p['colours'][0]['name']; $c2 = ($p['colours'][1] ?? $p['colours'][0])['name'];
?>
<a class="card" href="<?= product_url($p) ?>">
  <div class="img">
    <div class="main" style="width:100%;height:100%;display:grid;place-items:center"><?= product_visual($p, $c1) ?></div>
    <div class="alt"><?= product_visual($p, $c2) ?></div>
    <span class="tag"><?= e($p['status'] === 'Sold' ? 'Sold' : availability_label($p)) ?></span>
  </div>
  <h3><?= e($p['name']) ?></h3>
  <div class="meta"><?= e($p['silhouette'] . ', ' . $p['craft_name']) ?></div>
  <div class="row2"><span class="price"><?= money((int) $p['base_price']) ?></span>
    <span class="dots" aria-label="Colours: <?= e(implode(', ', array_column($p['colours'], 'name'))) ?>"><?php foreach ($p['colours'] as $c): ?><span style="background:<?= e($c['hex']) ?>" title="<?= e($c['name']) ?>"></span><?php endforeach; ?></span></div>
</a>
