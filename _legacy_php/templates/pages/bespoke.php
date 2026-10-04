<?php $COL = ['Black' => '#1f1c19', 'Oxblood' => '#4b1719', 'Burgundy' => '#5e1f2c', 'Tobacco' => '#6a4528', 'Cognac' => '#8f4b21', 'Dark Brown' => '#3c2619', 'Tan' => '#a66e3d', 'Forest Green' => '#26412f', 'Navy' => '#1e2b45', 'Beige' => '#c9b28f', 'Ivory' => '#e9e0cc']; ?>
<section class="dark"><div class="wrap split" style="padding-top:48px;padding-bottom:72px">
  <div><?php partial('crumbs', ['items' => [['Home', ''], ['Bespoke', null]], 'light' => true]); ?><h1 class="h1" style="margin-top:24px">Made for you.</h1>
    <p class="muted" style="margin:16px 0 28px">Choose a silhouette, a colour, a craft and your mark. See the estimate as you go. When custom artwork is involved, the house sends a design proof before anything is cut.</p>
    <div class="row"><a class="btn" href="<?= url('bespoke/build') ?>" data-track="begin_commission">Begin your commission</a><a class="btn ghost" href="<?= url('appointments') ?>">Book an appointment</a></div></div>
  <div class="art" style="background:#27463a"><?= draw_slot(['shape' => 'oxford', 'colour' => 'Navy', 'craft' => 'miniature', 'art' => 'botanical', 'initials' => 'A S', 'gold' => true], 'A navy Oxford with botanical painting and gold initials') ?></div>
</div></section>
<section class="section" id="custom-colour"><div class="wrap split">
  <div><h2 class="h2">Custom colour</h2><p style="margin-top:16px">Eleven colours in the house library, each built by hand in layers. Or bring your own: upload a reference or give us a colour code, and we send a photograph of a real swatch for your approval before making, because screens vary.</p><a class="tlink" href="<?= url('bespoke/build') ?>">Choose a colour</a></div>
  <div class="grid" style="grid-template-columns:repeat(4,1fr);gap:8px"><?php foreach ($COL as $n => $h): ?><div><div style="aspect-ratio:1;background:<?= $h ?>"><?= macro_slot('patina', $n, $n . ' patina swatch') ?></div><div class="small" style="margin-top:4px"><?= e($n) ?></div></div><?php endforeach; ?></div>
</div></section>
<section class="section panel" id="personalisation"><div class="wrap split">
  <div class="art wide"><?= draw_slot(['shape' => 'loafer', 'colour' => 'Tan', 'craft' => 'patina', 'initials' => 'M R', 'gold' => true, 'nails' => true], 'A tan loafer with gold initials and a brass nail monogram') ?></div>
  <div><h2 class="h2">Personalisation</h2><p style="margin-top:16px">Initials painted on the heel or insole, initials in gold leaf, or a monogram set in brass nails on the sole. Up to three letters. You see them on your pair as you type.</p><a class="tlink" href="<?= url('bespoke/build') ?>">Add your initials</a></div>
</div></section>
<section class="section" id="custom-artwork"><div class="wrap split">
  <div><h2 class="h2">Custom artwork</h2><p style="margin-top:16px">Choose from the house library of miniatures, tattoo and scarring designs, or bring a sketch, a family motif or the embroidery from an outfit. We prepare a digital proof and quote before making.</p><a class="tlink" href="<?= url('bespoke/build?craft=miniature') ?>">Start with artwork</a></div>
  <div class="grid g3" style="gap:8px"><?php foreach (['miniature', 'tattoo', 'carving'] as $k): ?><div class="art" style="aspect-ratio:1"><?= macro_slot($k, craft_colour($k)) ?></div><?php endforeach; ?></div>
</div></section>
<section class="section panel"><div class="wrap"><div class="head"><h2 class="h2">For occasions and collaborations</h2></div>
  <div class="grid g3">
    <a class="tile" href="<?= url('bespoke/wedding') ?>"><h3 class="h3">Wedding</h3><p class="small muted">Pairs for the groom, the bride and the family. Check your date.</p></a>
    <a class="tile" href="<?= url('bespoke/one-of-one') ?>"><h3 class="h3">One of One</h3><p class="small muted">Exceptional and collector pieces, made once.</p></a>
    <a class="tile" href="<?= url('bespoke/designers') ?>"><h3 class="h3">Designers &amp; Collectors</h3><p class="small muted">Designers, stylists, retailers, film and corporate gifting.</p></a>
  </div></div></section>
