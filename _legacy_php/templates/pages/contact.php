<?php $phone = setting('phone'); $email = setting('email_public'); ?>
<div class="wrap">
  <?php partial('crumbs', ['items' => [['Home', ''], ['Contact', null]]]); ?>
  <div style="margin-top:12px"><?php partial('page-head', ['title' => "Let's make something."]); ?></div>
  <div class="split" style="align-items:start;padding-bottom:96px">
    <div><p><?= e(setting('address_store')) ?></p>
      <?php if ($phone): ?><p><a href="tel:<?= e(preg_replace('/\s/', '', $phone)) ?>"><?= e($phone) ?></a></p><?php endif; ?>
      <?php if ($email): ?><p><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a></p><?php endif; ?>
      <div class="row" style="margin-top:16px"><?php partial('wa-button', ['label' => 'Message us', 'text' => 'Hello Tōramally,']); ?><a class="btn ghost" href="<?= e(setting('instagram')) ?>" target="_blank" rel="noopener">Instagram</a></div></div>
    <div><?php partial('form', ['kind' => 'contact', 'cta' => 'Send message', 'upload' => true, 'fields' => [['name', 'Name', 'text', null, true], ['email', 'Email', 'email', null, true], ['phone', 'Phone', 'tel', null, false], ['country', 'Country', 'text', null, false], ['type', 'Enquiry', 'select', ['General', 'Bespoke', 'Wedding', 'Designer', 'International'], true], ['message', 'Message', 'textarea', null, true]]]); ?></div>
  </div>
</div>
