<?php
/**
 * Product page. One template for every product, in the same order:
 * gallery · name, line, price · craft ladder · colour · size · availability
 * · Add to Bag or Commission (never both) · WhatsApp · Make it yours · accordions · related.
 * Vars: $p, $craft, $related
 */
$col = $p['colours'][0]['name'];
$women = $p['size_type'] === 'shoe_women';
$welted = str_contains((string) $p['construction'], 'Goodyear');
$service = $p['category'] === 'Service';
$sold = $p['status'] === 'Sold';
$sizes = size_options($p);
if ($service) $availTxt = 'Turnaround about ' . $p['lead_min'] . ' to ' . $p['lead_max'] . ' weeks from the day your pairs reach us.';
elseif ($p['availability'] === 'Ready to Ship') $availTxt = 'Ready to ship: ' . implode(', ', $p['ready']) . '.' . ($sizes ? ' Other sizes: made to order, ' . $p['lead_min'] . ' to ' . $p['lead_max'] . ' weeks.' : '');
elseif ($p['availability'] === 'Made to Order') $availTxt = 'Made to Order, ' . $p['lead_min'] . ' to ' . $p['lead_max'] . ' weeks. Each pair is made individually in our workshop. Time is part of the process.';
else $availTxt = 'Commission. Made after a conversation, a design proof and your approval. ' . $p['lead_min'] . ' to ' . $p['lead_max'] . ' weeks.';
$commission = $p['buy_mode'] === 'Commission';
$pageUrl = base_url() . substr(product_url($p), strlen(base_path()));

// Data for the page script: colours, photographs per colour, stock.
$imgMap = [];
foreach ($p['images'] as $img) {
    $cname = '*'; foreach ($p['colours'] as $c) if ($c['id'] && $c['id'] == $img['colour_id']) $cname = $c['name'];
    $view = in_array($img['kind'], ['hero', 'angle'], true) ? 'side' : $img['kind'];
    if (!isset($imgMap[$cname][$view])) $imgMap[$cname][$view] = ['src' => url($img['path']), 'alt' => $img['alt'] ?: $p['name']];
}
$pd = ['slug' => $p['slug'], 'name' => $p['name'], 'price' => (int) $p['base_price'], 'craft' => $p['craft'], 'drawing' => $p['drawing'] + ['shape' => 'loafer'],
       'colours' => array_map(fn($c) => ['name' => $c['name'], 'price_diff' => (int) $c['price_diff']], $p['colours']), 'images' => (object) $imgMap,
       'ready' => $p['ready'], 'avail' => $p['availability'], 'lead' => [(int) $p['lead_min'], (int) $p['lead_max']], 'women' => $women, 'service' => $service];

$buyBtn = $sold
  ? '<a class="btn full" href="' . url('bespoke/build?from=' . $p['slug']) . '">Commission something similar</a>'
  : ($commission ? '<a class="btn full" href="' . url('bespoke/build?from=' . $p['slug']) . '" data-track="begin_commission">Commission this pair</a>' : '<button class="btn full" data-add>Add to Bag</button>');
$GLOBALS['sticky_buy'] = '<div class="sticky-buy" id="stickyBuy" aria-hidden="true"><div style="flex:1;min-width:0"><div style="font-family:var(--serif);font-size:19px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' . e($p['name']) . '</div><div class="small" data-price>' . money((int) $p['base_price']) . '</div></div>'
  . ($sold ? '<span class="tag">Sold</span>' : ($commission ? '<a class="btn" href="' . url('bespoke/build?from=' . $p['slug']) . '">Commission</a>' : '<button class="btn" data-add>Add to Bag</button>')) . '</div>';
?>
<div class="wrap">
  <?php partial('crumbs', ['items' => [['Home', ''], [$service ? 'Services' : $p['category'], $service ? null : 'shop/' . strtolower($p['category'])], [$p['name'], null]]]); ?>
  <div class="pdp">
    <div>
      <div class="gal-main" data-gal tabindex="0" role="button" aria-label="Zoom image"><?= product_visual($p, $col) ?></div>
      <div class="thumbs" role="group" aria-label="Images">
        <button aria-pressed="true" data-view="side" aria-label="Side view"><?= product_visual($p, $col) ?></button>
        <button aria-pressed="false" data-view="macro" aria-label="Craft detail"><?= product_visual($p, $col, 'macro') ?></button>
        <button aria-pressed="false" data-view="box" aria-label="Presentation box"><div data-box style="width:100%"></div></button>
      </div>
    </div>
    <div class="pinfo">
      <h1><?= e($p['name']) ?></h1>
      <p class="poetic"><?= e($p['poetic']) ?></p>
      <div class="price" data-price style="font-size:20px"><?= money((int) $p['base_price']) ?></div>
      <div class="blk" style="margin-top:16px"><?php partial('ladder-indicator', ['slug' => $p['craft']]); ?>
        <p class="small muted" style="margin:6px 0 0"><?= e($p['silhouette']) ?><?= $p['last_name'] ? ', last ' . e($p['last_name']) : '' ?><?= $p['construction'] ? ', ' . e(mb_strtolower($p['construction'])) : '' ?></p></div>
      <?php if (!$service): ?>
      <div class="blk"><div class="label" style="margin-bottom:10px">Colour: <span data-colname style="text-transform:none;letter-spacing:0;font-size:14px"><?= e($col) ?></span></div>
        <div class="swatches" role="group" aria-label="Colour"><?php foreach ($p['colours'] as $i => $c): ?><button class="sw" style="background:<?= e($c['hex']) ?>" data-col="<?= e($c['name']) ?>" data-diff="<?= (int) $c['price_diff'] ?>" aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>" aria-label="<?= e($c['name']) ?>"></button><?php endforeach; ?></div></div>
      <?php endif; ?>
      <?php if ($sizes && !$sold): ?>
      <div class="blk">
        <div class="row" style="justify-content:space-between;margin-bottom:10px"><label class="label" for="sizeSel"><?= $p['size_type'] === 'belt' ? 'Waist' : 'Size' ?></label>
          <?php if ($p['size_type'] !== 'belt'): ?><div class="toggle" role="group" aria-label="Size system"><?php foreach (['India', 'UK', 'EU', 'US'] as $s): ?><button type="button" data-sys="<?= $s ?>" aria-pressed="<?= $s === 'UK' ? 'true' : 'false' ?>"><?= $s ?></button><?php endforeach; ?></div><?php endif; ?></div>
        <div class="field" style="margin:0" id="sizeField"><select id="sizeSel" aria-describedby="sizeErr"><option value=""></option><?php foreach ($sizes as $s): ?><option value="<?= e($s) ?>"><?= e($s) ?></option><?php endforeach; ?></select><div class="err" id="sizeErr">Please choose a size.</div></div>
        <div class="row small" style="margin-top:10px"><a href="<?= url('size-guide') ?>" data-track="size_guide_open">Find your size</a><?php partial('wa-button', ['label' => 'Help me find my size', 'text' => "Hello Tōramally, I need help with my size for {$p['name']}. My foot measures ___ cm.", 'class' => 'tlink']); ?></div>
      </div>
      <?php endif; ?>
      <?php if ($service): ?><div class="blk"><h2 class="h3">What is included</h2><p style="margin-top:8px"><?= e($p['story']) ?></p></div><?php endif; ?>
      <div class="blk"><span class="tag"><?= e($sold ? 'Sold' : availability_label($p)) ?></span><p class="small" style="margin:10px 0 0"><?= e($sold ? 'This piece has found its owner. We can make something in its spirit.' : $availTxt) ?></p></div>
      <div class="blk"><?= $buyBtn ?><div style="margin-top:10px"><?php partial('wa-button', ['label' => 'Ask about this pair', 'text' => "Hello Tōramally, I would like to ask about {$p['name']} ({$p['silhouette']}). $pageUrl", 'class' => 'btn ghost full']); ?></div></div>
      <?php if (!$service): ?>
      <div class="blk"><h2 class="h3">Make it yours</h2><p class="small" style="margin:8px 0 12px">Change the colour, add initials in paint or gold, a brass-nail monogram on the sole, or your own artwork (Upload ref. Image in the builder).</p><a class="btn ghost full" href="<?= url('bespoke/build?from=' . $p['slug']) ?>">Customise this pair</a></div>
      <?php endif; ?>
      <div class="box-note"><div data-box style="width:72px;flex:none"></div><p class="small" style="margin:0">Every pair arrives in the Tōramally wooden box, lined in deep green, with shoe bags and a care card.</p></div>
      <div class="acc" style="margin-top:28px">
        <details open><summary>The piece</summary><div class="in"><p><?= e($p['story'] && !$service ? $p['story'] : $p['poetic'] . ' ' . strtok((string) ($craft['description'] ?? ''), '.') . '.') ?></p></div></details>
        <?php if ($craft): ?><details><summary>The craft</summary><div class="in"><p><?= e($craft['description']) ?></p><a class="tlink" href="<?= url('craft/' . $craft['slug']) ?>">About <?= e($craft['name']) ?></a></div></details><?php endif; ?>
        <?php if (!$service): ?>
        <details><summary>The silhouette</summary><div class="in"><p><?= e($p['silhouette']) ?><?= $p['last_name'] ? ' on the ' . e($p['last_name']) . ' last' : '' ?>. Construction: <?= e($p['construction']) ?>.<?= $welted ? ' The upper is stitched to a welt and the welt to the sole, so the pair can be resoled by the workshop.' : '' ?></p></div></details>
        <details><summary>The leather</summary><div class="in"><p><?= $p['craft'] === 'velvet' ? 'Velvet upper with a calf patch, leather lined.' : 'Full-grain calfskin, coloured by hand in the house patina. Leather lined.' ?></p></div></details>
        <details><summary>The fit</summary><div class="in"><p>True to UK size for most feet. If you are between sizes, choose the larger, or message us with your foot measurement.</p><a class="tlink" href="<?= url('size-guide') ?>">Size guide</a></div></details>
        <?php if ($craft): ?><details><summary>The making</summary><div class="in"><p><?= e($craft['how_1'] . ' ' . $craft['how_2'] . ' ' . $craft['how_3']) ?></p></div></details><?php endif; ?>
        <?php endif; ?>
        <details><summary>Care</summary><div class="in"><p>Brush after wear, rest a day between wears, and keep in the cloth bags with shoe trees. <?= $p['craft'] === 'miniature' ? 'Polish around the painting, never over it.' : ($p['craft'] === 'velvet' ? 'Brush velvet with a soft brush in the direction of the pile.' : 'A neutral cream every few weeks keeps the colour deep.') ?></p><a class="tlink" href="<?= url('care') ?>">Full care guide</a></div></details>
        <details><summary>Delivery &amp; returns</summary><div class="in"><p>Insured delivery across India and worldwide. International orders: duties are paid by the recipient on delivery; we give an estimate before you confirm. Ready-to-ship pairs, unworn and in the box, can be exchanged for size within 7 days. Personalised and made-to-order pairs are not returnable, and are remade if faulty.</p><a class="tlink" href="<?= url('returns') ?>">Full policy</a></div></details>
      </div>
    </div>
  </div>
  <?php if ($related): ?>
  <section class="section"><div class="head"><h2 class="h2">More of this <?= $related[0]['craft'] === $p['craft'] ? 'craft' : 'silhouette' ?></h2></div>
    <div class="grid g4"><?php foreach ($related as $r) partial('product-card', ['p' => $r]); ?></div></section>
  <?php endif; ?>
</div>
<script type="application/json" id="productData"><?= json_encode($pd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
