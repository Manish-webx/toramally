<?php
/**
 * Listing page. Filters live in the address bar (?craft=miniature|tattoo),
 * so a filtered view can be shared. Within a filter: OR. Across filters: AND.
 */
$crumbs = [['Home', ''], ['Shop', $cat ? 'shop' : null]];
if ($cat) $crumbs[] = [$cat, $line ? 'shop/' . $catKey : null];
if ($line) $crumbs[] = [$line, null];
$activeCount = array_sum(array_map('count', $sel));
ob_start(); ?>
<?php foreach ($defs as $k => [$label, $opts]): $rows = '';
  foreach ($opts as [$v, $t]) {
    $n = $counts[$k][$v] ?? 0;
    if (!$n && !in_array($v, $sel[$k], true)) continue;
    $rows .= '<label><input type="checkbox" data-f="' . e($k) . '" value="' . e($v) . '"' . (in_array($v, $sel[$k], true) ? ' checked' : '') . '>'
      . ($k === 'colour' ? '<span class="dots"><span style="background:' . e($colours[$v] ?? '#999') . '"></span></span>' : '')
      . e($t) . ' <span class="n">(' . $n . ')</span></label>';
  }
  if ($rows): ?><div class="fgroup"><h4><?= e($label) ?></h4><?= $rows ?></div><?php endif;
endforeach; ?>
<?php $filterHtml = ob_get_clean(); ?>
<div class="wrap" data-shop>
  <?php partial('crumbs', ['items' => $crumbs]); ?>
  <div class="head" style="margin-top:12px"><div><h1 class="h1"><?= e($title) ?></h1><p style="margin-top:8px"><?= e($sub) ?></p></div>
    <label class="row small"><span class="label">Sort</span><select data-sort style="min-height:40px;border:1px solid var(--line);background:var(--ivory-50);padding:0 10px">
      <?php foreach (['featured' => 'Featured', 'new' => 'New', 'low' => 'Price low to high', 'high' => 'Price high to low'] as $v => $t): ?><option value="<?= $v ?>"<?= $sort === $v ? ' selected' : '' ?>><?= $t ?></option><?php endforeach; ?>
    </select></label></div>
  <?php if ($cat === 'Men'): ?>
    <nav class="seg" aria-label="Line"><a href="<?= url('shop/men') ?>" aria-current="<?= !$line ? 'true' : 'false' ?>">All</a><a href="<?= url('shop/men/classic') ?>" aria-current="<?= $line === 'Classic' ? 'true' : 'false' ?>">Classic</a><a href="<?= url('shop/men/special-occasion') ?>" aria-current="<?= $line === 'Special Occasion' ? 'true' : 'false' ?>">Special Occasion</a></nav>
  <?php endif; ?>
  <div class="chips" role="group" aria-label="Silhouette"><?php foreach (array_unique(array_column($pool, 'silhouette')) as $s): ?><button class="chip" data-silchip="<?= e($s) ?>" aria-pressed="<?= in_array($s, $sel['sil'], true) ? 'true' : 'false' ?>"><?= e($s) ?></button><?php endforeach; ?></div>
  <div class="list-layout" style="padding-bottom:96px">
    <aside class="filters" aria-label="Filters" data-filterrail><?= $filterHtml ?></aside>
    <div>
      <div class="tools"><span class="small muted"><?= count($results) ?> <?= count($results) === 1 ? 'pair' : 'pairs' ?></span><button class="btn ghost mfilter" data-open="filterSheet" style="min-height:40px">Filter<?= $activeCount ? " ($activeCount)" : '' ?></button></div>
      <?php if ($activeCount): ?><div class="row" style="margin-bottom:20px">
        <?php foreach ($defs as $k => [, $opts]) foreach ($sel[$k] as $v): $lbl = $v; foreach ($opts as [$ov, $ot]) if ($ov === $v) $lbl = $ot; ?>
          <button class="chip" data-rm="<?= e($k) ?>" data-v="<?= e($v) ?>" aria-label="Remove <?= e($lbl) ?>"><?= e($lbl) ?> ✕</button>
        <?php endforeach; ?><button class="tlink" data-clear>Clear all</button></div><?php endif; ?>
      <?php if ($results): ?>
        <div class="grid g3"><?php foreach ($results as $p) partial('product-card', ['p' => $p]); ?></div>
      <?php else: ?>
        <div class="notice"><p class="serif-i" style="font-size:24px;margin:0 0 8px">Not quite what you imagined? Commission it.</p><div class="row"><a class="btn" href="<?= url('bespoke/build') ?>">Build your pair</a><button class="tlink" data-clear>Clear all filters</button></div></div>
        <h3 class="h3" style="margin:40px 0 16px">Closest to your search</h3>
        <div class="grid g3"><?php foreach (array_slice($pool, 0, 3) as $p) partial('product-card', ['p' => $p]); ?></div>
      <?php endif; ?>
    </div>
  </div>
</div>
