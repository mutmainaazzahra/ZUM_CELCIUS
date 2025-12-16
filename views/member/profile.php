<?php include '../views/layout/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="glass-card border-0 p-0 overflow-hidden h-auto">

                <div class="p-4 border-bottom border-secondary bg-black bg-opacity-10 text-center">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-person-circle me-2 text-warning"></i> Profil Saya</h5>
                </div>

                <div class="p-4 px-5">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger border-0 text-white text-center shadow-sm mb-4" style="background: rgba(220, 53, 69, 0.7);">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <?php
                            if ($_GET['error'] == 'username_space') echo "Username tidak boleh mengandung spasi.";
                            else echo "Gagal memperbarui profil.";
                            ?>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
                        <div class="alert alert-success border-0 text-white text-center shadow-sm mb-4" style="background: rgba(25, 135, 84, 0.7);">
                            <i class="bi bi-check-circle-fill me-1"></i> Profil berhasil diperbarui!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?page=update_profile" enctype="multipart/form-data">

                        <!-- Foto Profil-->
                        <div class="text-center mb-5 position-relative">
                            <div class="d-inline-block position-relative">
                                <?php
                                $photo = !empty($user['profile_photo']) ? "uploads/" . $user['profile_photo'] : "https://via.placeholder.com/150/cccccc/ffffff?text=User";
                                ?>
                                <img src="<?php echo $photo; ?>" alt="Foto Profil" class="rounded-circle shadow-lg" style="width: 140px; height: 140px; object-fit: cover; border: 4px solid rgba(255,255,255,0.2);">

                                <!-- Tombol Kamera -->
                                <label for="photo" class="position-absolute bottom-0 end-0 btn btn-warning rounded-circle shadow p-0 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border: 3px solid rgba(255,255,255,0.2); cursor: pointer;" title="Ganti Foto" data-bs-toggle="tooltip">
                                    <i class="bi bi-camera-fill text-dark"></i>
                                </label>
                                <input type="file" name="photo" id="photo" class="form-control d-none" accept="image/*" onchange="previewImage(this)">
                            </div>
                            <div class="mt-3 text-muted small">Ketuk ikon kamera untuk mengubah foto</div>
                        </div>

                        <!-- Form Inputs -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-0 text-dark"><i class="bi bi-person-fill fs-5"></i></span>
                                <input type="text" name="username" class="form-control text-dark bg-white" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-0 text-dark"><i class="bi bi-envelope-fill fs-5"></i></span>
                                <input type="email" name="email" class="form-control text-dark bg-white" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning rounded-pill py-3 fw-bold text-dark shadow-sm scale-hover">
                                <i class="bi bi-save-fill me-2"></i> SIMPAN PERUBAHAN
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('img[alt="Foto Profil"]').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

<?php include '../views/layout/footer.php'; ?>