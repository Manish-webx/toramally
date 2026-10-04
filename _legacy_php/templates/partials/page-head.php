<?php /* Page heading with the thin line beneath. Content begins below the line. Vars: $title, $sub */ ?>
<div class="head"><div><h1 class="h1"><?= e($title) ?></h1><?php if (!empty($sub)): ?><p style="margin-top:8px"><?= e($sub) ?></p><?php endif; ?></div><?= $right ?? '' ?></div>
