<?php
/**
 * Customer file uploads (reference images, owned-pair photos).
 * Files are checked by their real content (not just the name), renamed to
 * random names, and stored in /storage/uploads, which the web server is
 * told never to serve directly. Admins view them through a protected link.
 */
const UPLOAD_MAX_BYTES = 20 * 1024 * 1024;
const UPLOAD_MAX_FILES = 5;
const UPLOAD_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf', 'image/webp' => 'webp'];

/**
 * Save files from $_FILES[$field] for an owner record.
 * Returns ['ok' => bool, 'error' => string|null, 'count' => int].
 */
function save_uploads(string $field, string $ownerType, int $ownerId, string $kind = 'reference'): array
{
    if (empty($_FILES[$field]) || empty($_FILES[$field]['name'])) return ['ok' => true, 'count' => 0];
    $f = $_FILES[$field];
    $names = (array) $f['name'];
    if (count(array_filter($names)) > UPLOAD_MAX_FILES) return ['ok' => false, 'error' => 'Please choose up to five files.'];
    $dir = 'storage/uploads/' . $ownerType . '/' . date('Y/m');
    if (!is_dir(ROOT . '/' . $dir)) mkdir(ROOT . '/' . $dir, 0755, true);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $count = 0;
    foreach ($names as $i => $name) {
        if ($name === '') continue;
        $tmp = ((array) $f['tmp_name'])[$i];
        $err = ((array) $f['error'])[$i];
        $size = ((array) $f['size'])[$i];
        if ($err !== UPLOAD_ERR_OK || $size > UPLOAD_MAX_BYTES || !is_uploaded_file($tmp)) {
            return ['ok' => false, 'error' => 'We could not upload that file. Please use a JPG, PNG or PDF under 20 MB.'];
        }
        $mime = $finfo->file($tmp);
        if (!isset(UPLOAD_TYPES[$mime])) {
            return ['ok' => false, 'error' => 'We could not upload that file. Please use a JPG, PNG or PDF under 20 MB.'];
        }
        $rel = $dir . '/' . bin2hex(random_bytes(16)) . '.' . UPLOAD_TYPES[$mime];
        if (!move_uploaded_file($tmp, ROOT . '/' . $rel)) return ['ok' => false, 'error' => 'Upload failed. Please try again.'];
        db_insert('uploads', ['owner_type' => $ownerType, 'owner_id' => $ownerId, 'path' => $rel, 'original_name' => mb_substr(basename($name), 0, 190),
                              'mime' => $mime, 'size_bytes' => $size, 'kind' => $kind]);
        $count++;
    }
    return ['ok' => true, 'count' => $count];
}
