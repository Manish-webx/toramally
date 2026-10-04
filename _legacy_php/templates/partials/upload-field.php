<?php /* Upload ref. Image field (JPG, PNG or PDF, up to five files, 20 MB each). */ $uid = 'up' . bin2hex(random_bytes(3)); ?>
<div class="field"><span>Reference (optional)</span>
  <div class="upload"><input type="file" id="<?= $uid ?>" name="files[]" accept=".jpg,.jpeg,.png,.pdf" multiple data-upload><label class="btn ghost" for="<?= $uid ?>">Upload ref. Image</label><span class="small muted" data-upnames></span></div>
  <div class="err" data-uperr></div>
</div>
