<?php /** Worn By. Only people with written permission on file are shown. Vars: $people */ ?>
<div class="wrap" style="padding-bottom:96px">
  <?php partial('crumbs', ['items' => [['Home', ''], ['The House', 'house'], ['Worn by', null]]]); ?>
  <div style="margin-top:12px"><?php partial('page-head', ['title' => 'Worn by', 'sub' => 'Clients and friends of the house, in their Tōramally pairs. Shared with their permission.']); ?></div>
  <?php if ($people): ?>
    <div class="grid g3"><?php foreach ($people as $c): ?>
      <figure class="tile" style="margin:0"><div class="img"><?= $c['photo_path'] ? '<img src="' . e(url($c['photo_path'])) . '" alt="' . e($c['name'] . ' wearing Tōramally') . '" loading="lazy">' : macro_slot('patina', 'Oxblood') ?></div>
        <figcaption><h3 class="h3"><?= e($c['name']) ?></h3><p class="small muted"><?= e(implode('. ', array_filter([$c['occasion'], $c['pair_worn']]))) ?></p></figcaption></figure>
    <?php endforeach; ?></div>
  <?php else: ?>
    <p class="serif-i" style="font-size:24px">The first portraits are being prepared.</p>
    <p>In the meantime, see the house on <a href="<?= e(setting('instagram')) ?>" target="_blank" rel="noopener">Instagram</a>.</p>
  <?php endif; ?>
</div>
