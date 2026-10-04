<?php $f = $posts[0] ?? null; $rest = array_slice($posts, 1); $cslug = fn($a) => craft_by_id((int) $a['craft_id'])['slug'] ?? 'patina'; ?>
<div class="wrap">
  <?php partial('crumbs', ['items' => [['Home', ''], ['Journal', null]]]); ?>
  <div style="margin-top:12px"><?php partial('page-head', ['title' => 'Journal', 'sub' => 'Notes on craft, style, materials, people and places.']); ?></div>
  <?php if ($f): ?>
  <a class="split" href="<?= url('journal/' . $f['slug']) ?>" style="text-decoration:none"><div class="art wide"><?= $f['cover_path'] ? '<img src="' . e(url($f['cover_path'])) . '" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover">' : macro_slot($cslug($f), craft_colour($cslug($f))) ?></div>
    <div><p class="label brass"><?= e($f['category']) ?></p><h2 class="h1" style="margin:8px 0 12px"><?= e($f['title']) ?></h2><p><?= e($f['dek']) ?></p><span class="tlink">Read</span></div></a>
  <?php endif; ?>
  <div class="grid g3 section"><?php foreach ($rest as $a): ?>
    <a class="tile" href="<?= url('journal/' . $a['slug']) ?>"><div class="img" style="aspect-ratio:3/2"><?= $a['cover_path'] ? '<img src="' . e(url($a['cover_path'])) . '" alt="" loading="lazy">' : macro_slot($cslug($a), craft_colour($cslug($a))) ?></div>
      <p class="label brass" style="margin-top:14px"><?= e($a['category']) ?></p><h3 class="h3" style="margin-top:4px"><?= e($a['title']) ?></h3><p class="small muted"><?= e($a['dek']) ?></p></a>
  <?php endforeach; ?></div>
  <?php if ($soon): ?><div style="padding-bottom:96px"><div class="head"><h2 class="h3">In preparation</h2></div><p class="small muted"><?= e(implode('. ', array_column($soon, 'title'))) ?>.</p></div><?php endif; ?>
</div>
