<div class="wrap" style="padding-bottom:96px">
  <?php partial('crumbs', ['items' => [['Home', ''], ['Search', null]]]); ?>
  <div style="margin-top:12px"><?php partial('page-head', ['title' => 'Search']); ?></div>
  <form action="<?= url('search') ?>" method="get" role="search" class="row" style="margin-bottom:32px"><label class="vh" for="sq">Search</label><input id="sq" name="q" value="<?= e($q) ?>" class="field" style="flex:1;min-height:48px;border:1px solid var(--line);background:var(--ivory-50);padding:0 12px;margin:0"><button class="btn" type="submit">Search</button></form>
  <?php $any = array_filter($results); if ($q !== '' && !$any): ?>
    <p>Nothing found for “<?= e($q) ?>”. Try Belgian, Miniature or Wedding.</p><p class="serif-i" style="font-size:22px">Not quite what you imagined? <a href="<?= url('bespoke/build') ?>">Commission it.</a></p>
  <?php endif; ?>
  <div class="sres"><?php foreach ($results as $g => $list) if ($list): ?><h4><?= e($g) ?></h4><?php foreach ($list as $r): ?><a href="<?= e($r['h']) ?>"><?= e($r['t']) ?></a><?php endforeach; endif; ?></div>
</div>
