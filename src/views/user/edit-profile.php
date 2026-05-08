<?php require_once BASE_PATH . '/views/layouts/header.php'; ?>
<?php require_once BASE_PATH . '/views/layouts/navbar.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Profile</h5>
                </div>
                <div class="card-body">
                    <?= render_flash() ?>

                    <!--
                        FIX: enctype="multipart/form-data" MUST be set for file upload to work.
                        Previously this was sometimes missing, causing $_FILES to be empty.
                    -->
                    <form method="POST" enctype="multipart/form-data">

                        <!-- Current profile image preview -->
                        <div class="text-center mb-3">
                            <?php if (!empty($user['profile_image'])): ?>
                                <img id="imgPreview"
                                     src="<?= upload_url($user['profile_image']) ?>"
                                     class="rounded-circle border"
                                     style="width:100px;height:100px;object-fit:cover;">
                            <?php else: ?>
                                <div id="imgPreview"
                                     class="rounded-circle bg-secondary text-white d-inline-flex
                                            align-items-center justify-content-center"
                                     style="width:100px;height:100px;font-size:40px;">
                                    <i class="bi bi-person"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Profile Image</label>
                            <input type="file"
                                   name="profile_image"
                                   id="profileImageInput"
                                   class="form-control"
                                   accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">JPG, PNG or WebP. Max 5MB. Leave empty to keep current.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="<?= htmlspecialchars($user['name']) ?>"
                                   required
                                   maxlength="100">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel"
                                   name="phone"
                                   class="form-control"
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                   maxlength="20">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Interface Language</label>
                            <select name="language" class="form-select">
                                <?php foreach (['en' => 'English', 'ar' => 'Arabic', 'fr' => 'French', 'de' => 'German', 'es' => 'Spanish'] as $code => $label): ?>
                                    <option value="<?= $code ?>" <?= ($user['language'] ?? 'en') === $code ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Changes
                            </button>
                            <a href="<?= page_url('profile') ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Live image preview script -->
<script>
document.getElementById('profileImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    // Client-side size check (5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('File too large. Maximum size is 5MB.');
        this.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(ev) {
        const preview = document.getElementById('imgPreview');
        if (preview.tagName === 'IMG') {
            preview.src = ev.target.result;
        } else {
            // Replace div with img element
            const img = document.createElement('img');
            img.id        = 'imgPreview';
            img.className = 'rounded-circle border';
            img.style     = 'width:100px;height:100px;object-fit:cover;';
            img.src       = ev.target.result;
            preview.parentNode.replaceChild(img, preview);
        }
    };
    reader.readAsDataURL(file);
});
</script>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>