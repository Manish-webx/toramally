<?php
/**
 * Enquiry form builder. Sent by JavaScript to /api/form.
 * Vars: $kind, $fields = [[name, label, type, options|null, required], ...], $cta, $upload (bool)
 */
?>
<form data-form="<?= e($kind) ?>" novalidate enctype="multipart/form-data">
  <input class="hp hp-input" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
  <?php foreach ($fields as [$name, $label, $type, $opts, $req]): ?>
    <label class="field"><span><?= e($label) ?><?= $req ? '' : ' (optional)' ?></span>
      <?php if ($type === 'select'): ?>
        <select name="<?= e($name) ?>"<?= $req ? ' required' : '' ?>><option value=""></option><?php foreach ($opts as $o): ?><option><?= e($o) ?></option><?php endforeach; ?></select>
      <?php elseif ($type === 'textarea'): ?>
        <textarea name="<?= e($name) ?>"<?= $req ? ' required' : '' ?>></textarea>
      <?php else: ?>
        <input name="<?= e($name) ?>" type="<?= e($type) ?>"<?= $req ? ' required' : '' ?><?= $type === 'email' ? ' autocomplete="email"' : ($name === 'name' ? ' autocomplete="name"' : ($type === 'tel' ? ' autocomplete="tel"' : '')) ?><?= $type === 'date' ? ' min="' . date('Y-m-d') . '"' : '' ?>>
      <?php endif; ?>
      <div class="err"><?= $type === 'email' ? 'Please enter a valid email address.' : 'Please complete this field.' ?></div>
    </label>
  <?php endforeach; ?>
  <?php if (!empty($upload)) partial('upload-field'); ?>
  <button class="btn" type="submit"><?= e($cta) ?></button>
  <div class="notice ok form-msg" data-formok tabindex="-1" hidden></div>
</form>
