<?php /** Policy pages (Privacy, Terms, Shipping, Returns, Cookies), edited in Admin > Content > Pages. Vars: $pg */ ?>
<div class="wrap prose" style="max-width:820px;padding-bottom:96px">
  <?php partial('crumbs', ['items' => [['Home', ''], [$pg['title'], null]]]); ?>
  <div style="margin-top:12px"><?php partial('page-head', ['title' => $pg['title']]); ?></div>
  <?= $pg['body_html'] /* trusted HTML from the admin editor */ ?>
  <?php if ($pg['slug'] === 'cookies'): ?><button class="btn ghost" data-resetcookie>Change cookie choice</button><?php endif; ?>
  <p class="small muted" style="margin-top:32px">Last updated <?= e(date('j F Y', strtotime($pg['updated_at']))) ?>.</p>
</div>
