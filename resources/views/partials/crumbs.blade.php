<nav class="crumbs" aria-label="Breadcrumb"{!! !empty($light) ? ' style="color:#c9c4b4"' : '' !!}>
@php
    $list = $items ?? $crumbs ?? [];
    $out = [];
    foreach ($list as $crumb) {
        if (is_array($crumb) && isset($crumb['t'])) {
            $t = $crumb['t'];
            $h = $crumb['h'] ?? null;
        } elseif (is_array($crumb)) {
            $t = $crumb[0] ?? '';
            $h = $crumb[1] ?? null;
        } else {
            $t = (string) $crumb;
            $h = null;
        }
        $out[] = !empty($h) ? '<a href="' . e(url($h)) . '">' . e($t) . '</a>' : '<span aria-current="page">' . e($t) . '</span>';
    }
    echo implode(' / ', $out);
@endphp
</nav>
