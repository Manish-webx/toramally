<?php /** Journal article. body_html is written in the admin editor. Vars: $a, $craft, $related */ ?>
<article class="wrap prose" style="max-width:820px">
  <?php partial('crumbs', ['items' => [['Home', ''], ['Journal', 'journal'], [$a['title'], null]]]); ?>
  <p class="label brass" style="margin-top:32px"><?= e($a['category']) ?></p>
  <h1 class="h1" style="margin:8px 0 12px"><?= e($a['title']) ?></h1>
  <p class="serif-i" style="font-size:24px;color:#5d5a52"><?= e($a['dek']) ?></p>
  <div class="art wide" style="margin:32px 0"><?= $a['cover_path'] ? '<img src="' . e(url($a['cover_path'])) . '" alt="" style="width:100%;height:100%;object-fit:cover">' : macro_slot($craft['slug'] ?? 'patina', craft_colour($craft['slug'] ?? 'patina')) ?></div>
  <?= $a['body_html'] /* trusted HTML from the admin editor */ ?>
  <div class="row" style="margin:32px 0"><?php if ($craft): ?><a class="tlink" href="<?= url('craft/' . $craft['slug']) ?>">About <?= e($craft['name']) ?></a><?php endif; ?><button class="tlink" data-sharepage>Share</button></div>
</article>
<?php if ($related): ?><section class="section panel"><div class="wrap"><div class="head"><h2 class="h2">Pairs from this story</h2></div><div class="grid g3"><?php foreach ($related as $p) partial('product-card', ['p' => $p]); ?></div></div></section><?php endif; ?>
