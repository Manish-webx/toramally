<?php /* The craft ladder: one thin brass line, one tile per craft. */ ?>
<div class="ladder" role="region" aria-label="The craft ladder" tabindex="0"><ol>
<?php foreach (crafts() as $l): ?>
  <li><a href="<?= url('craft/' . $l['slug']) ?>" data-track="select_craft" data-craft="<?= e($l['slug']) ?>">
    <div class="macro"><?= macro_slot($l['slug'], craft_colour($l['slug'])) ?></div>
    <h3 class="h3"><?= e($l['name']) ?></h3><div class="scale"><?= e($l['scale_word'] . '. ' . $l['tagline']) ?></div>
    <div class="from"><?= $l['from_price'] ? 'from ' . money((int) $l['from_price']) : 'By commission' ?></div></a></li>
<?php endforeach; ?>
</ol></div>
