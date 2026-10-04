<?php
/** Videos. YouTube and Vimeo links are embedded; nothing plays with sound until pressed. Vars: $videos */
$embed = function (string $u): ?string {
    if (preg_match('~(?:youtu\.be/|v=|embed/)([\w-]{11})~', $u, $m)) return 'https://www.youtube-nocookie.com/embed/' . $m[1];
    if (preg_match('~vimeo\.com/(\d+)~', $u, $m)) return 'https://player.vimeo.com/video/' . $m[1];
    return null;
};
?>
<div class="wrap" style="padding-bottom:96px">
  <?php partial('crumbs', ['items' => [['Home', ''], ['The House', 'house'], ['Videos', null]]]); ?>
  <div style="margin-top:12px"><?php partial('page-head', ['title' => 'Videos', 'sub' => 'Films of the making: brush, beam, needle and blade.']); ?></div>
  <?php if ($videos): ?>
    <div class="grid g2"><?php foreach ($videos as $v): $src = $embed($v['url']); ?>
      <figure style="margin:0"><div class="art wide"><?php if ($src): ?><iframe src="<?= e($src) ?>" title="<?= e($v['title']) ?>" loading="lazy" allow="encrypted-media; picture-in-picture" allowfullscreen style="width:100%;height:100%;border:0"></iframe><?php else: ?><video src="<?= e(url($v['url'])) ?>" controls preload="none" style="width:100%;height:100%"<?= $v['poster_path'] ? ' poster="' . e(url($v['poster_path'])) . '"' : '' ?>></video><?php endif; ?></div>
        <figcaption><h3 class="h3" style="margin-top:12px"><?= e($v['title']) ?></h3><?php if ($v['caption']): ?><p class="small muted"><?= e($v['caption']) ?></p><?php endif; ?></figcaption></figure>
    <?php endforeach; ?></div>
  <?php else: ?>
    <p class="serif-i" style="font-size:24px">Films from the workshop are on their way.</p>
    <p>Until then, watch the making on <a href="<?= e(setting('instagram')) ?>" target="_blank" rel="noopener">Instagram</a>.</p>
  <?php endif; ?>
</div>
