<?php /* Breadcrumb. Vars: $items = [[label, path|null], ...], optional $light */ ?>
<nav class="crumbs" aria-label="Breadcrumb"<?= !empty($light) ? ' style="color:#c9c4b4"' : '' ?>><?php
$out = [];
foreach ($items as [$t, $h]) $out[] = $h !== null ? '<a href="' . e(url($h)) . '">' . e($t) . '</a>' : '<span aria-current="page">' . e($t) . '</span>';
echo implode(' / ', $out); ?></nav>
