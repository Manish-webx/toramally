<?php /* Seven dots on a line, the current craft filled. Vars: $slug */
$c = craft_by_slug($slug); $i = craft_index($slug); $all = crafts(); ?>
<div aria-label="Craft ladder: <?= e($c['name'] ?? '') ?>, rung <?= $i + 1 ?> of <?= count($all) ?>"><div class="lad-ind"><?php foreach ($all as $k => $l): ?><?= $k ? '<i></i>' : '' ?><b class="<?= $k === $i ? 'on' : '' ?>" title="<?= e($l['name']) ?>"></b><?php endforeach; ?></div>
<div class="lad-cap"><a href="<?= url('craft/' . $slug) ?>" style="text-decoration:none"><span class="serif-i" style="font-size:20px"><?= e($c['name'] ?? '') ?></span></a> <span class="brass small"><?= e($c['scale_word'] ?? '') ?></span></div></div>
