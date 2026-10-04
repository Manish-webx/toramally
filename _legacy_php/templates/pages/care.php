<?php $items = [['Daily care', 'Brush after every wear. Rest a pair for a day between wears. Keep shoe trees in, and store in the cloth bags.'], ['Patina care', 'A neutral cream every few weeks keeps the colour deep. Polish the toe and heel with a little wax and a drop of water. We can take patina darker at the house.'], ['Scarring care', 'Neutral cream and a soft brush. Avoid heavy coloured polish, which fills the finer lines.'], ['Miniature care', 'Polish around the painting, never over it. Wipe the painted area with a dry soft cloth.'], ['Tattoo care', 'Neutral cream only. Do not rub the ink with solvent or spirit.'], ['Carving care', 'A soft brush clears the cuts. Feed with neutral cream and let it dry before buffing.'], ['Velvet care', 'Brush with a soft brush in the direction of the pile. Keep dry; blot, do not rub.'], ['Storage', 'Cloth bags, shoe trees, away from direct sun and heaters. Keep the wooden box for travel.'], ['Monsoon care', 'Let wet pairs dry slowly at room temperature, stuffed with paper, never near heat. Feed with cream once dry.']]; ?>
<div class="wrap">
  <?php partial('crumbs', ['items' => [['Home', ''], ['Care', null]]]); ?>
  <div style="margin-top:12px"><?php partial('page-head', ['title' => 'Care', 'sub' => 'Every craft asks for a little different care.']); ?></div>
  <div class="acc" style="max-width:860px;padding-bottom:96px">
    <?php foreach ($items as $i => [$t, $d]): ?><details<?= $i === 0 ? ' open' : '' ?>><summary><?= e($t) ?></summary><div class="in"><p><?= e($d) ?></p></div></details><?php endforeach; ?>
    <div class="row" style="margin-top:32px"><a class="btn ghost" href="<?= url('restoration') ?>">Restoration</a><a class="btn ghost" href="<?= url('shoe-shine-service') ?>">The Shoe Shine Service</a></div>
  </div>
</div>
