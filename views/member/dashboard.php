<?php include '../views/layout/header.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold mb-0" style="color: var(--headline);"><i class="bi bi-list-check me-2 text-warning"></i> Dashboard Member</h2>
            <p class="text-muted">Kelola jadwal dan kegiatan harianmu.</p>
        </div>

        <?php if (isset($forecast['list'][0])): ?>
            <div class="glass-card px-4 py-2 d-flex align-items-center shadow-sm">
                <img src="https://openweathermap.org/img/wn/<?php echo $forecast['list'][0]['weather'][0]['icon']; ?>.png" width="40">
                <div class="ms-2 lh-1">
                    <div class="fw-bold" style="color: var(--headline);"><?php echo round($forecast['list'][0]['main']['temp']); ?>°C</div>
                    <small class="text-muted" style="font-size: 0.8rem;"><?php echo $forecast['city']['name']; ?></small>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success glass-card border-0 shadow-sm mb-4 d-flex align-items-center" style="color: #0f5132; background-color: #d1e7dd !important;">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>
                <?php
                if ($_GET['msg'] == 'added') echo "Aktivitas baru berhasil disimpan.";
                if ($_GET['msg'] == 'updated') echo "Aktivitas berhasil diperbarui.";
                if ($_GET['msg'] == 'deleted') echo "Aktivitas berhasil dihapus.";
                ?>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="glass-card h-100">
                <div class="p-4 border-bottom" style="border-color: var(--tertiary) !important;">
                    <h5 class="fw-bold m-0" style="color: var(--headline);"><i class="bi bi-plus-circle-fill me-2 text-warning"></i>Tambah Baru</h5>
                </div>
                <div class="p-4">
                    <form method="POST" action="index.php?page=dashboard">
                        <input type="hidden" name="add_activity" value="1">

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase" style="color: var(--paragraph); letter-spacing: 1px;">Nama Kegiatan</label>
                            <input type="text" name="name" class="form-control" placeholder="Misal: Lari Pagi" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase" style="color: var(--paragraph); letter-spacing: 1px;">Kategori</label>
                            <select name="type" class="form-select">
                                <option value="Outdoor">Outdoor (Luar Ruangan)</option>
                                <option value="Indoor">Indoor (Dalam Ruangan)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase" style="color: var(--paragraph); letter-spacing: 1px;">Waktu</label>
                            <div class="input-group">
                                <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                <input type="time" name="time" class="form-control" value="<?php echo date('H:i'); ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase" style="color: var(--paragraph); letter-spacing: 1px;">Catatan</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Detail kegiatan..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 rounded-pill py-2 fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="glass-card h-100 p-0 overflow-hidden">
                <div class="p-4 border-bottom" style="border-color: var(--tertiary) !important; background-color: var(--secondary);">
                    <h5 class="fw-bold m-0" style="color: var(--headline);"><i class="bi bi-calendar-check me-2"></i>Riwayat Aktivitas</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color: #fff;">
                            <tr>
                                <th class="ps-4 py-3 text-muted small fw-bold text-uppercase">Waktu</th>
                                <th class="text-muted small fw-bold text-uppercase">Kegiatan</th>
                                <th class="text-muted small fw-bold text-uppercase">Status</th>
                                <th class="text-end pe-4 text-muted small fw-bold text-uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($activities)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">Belum ada aktivitas. Ayo mulai produktif!</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($activities as $act): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold" style="color: var(--headline);"><?php echo date('d M', strtotime($act['date'])); ?></div>
                                            <div class="badge bg-light text-dark border mt-1">
                                                <i class="bi bi-clock me-1"></i> <?php echo date('H:i', strtotime($act['time'])); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold" style="color: var(--paragraph);"><?php echo htmlspecialchars($act['name']); ?></div>
                                            <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                                                <?php echo htmlspecialchars($act['notes']); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($act['type'] == 'Outdoor'): ?>
                                                <span class="badge bg-warning bg-opacity-25 text-dark border border-warning rounded-pill px-3">Outdoor</span>
                                            <?php else: ?>
                                                <span class="badge bg-info bg-opacity-25 text-dark border border-info rounded-pill px-3">Indoor</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-light border shadow-sm rounded-circle me-1 btn-edit-activity"
                                                data-bs-toggle="modal" data-bs-target="#editActivityModal"
                                                data-id="<?php echo $act['id']; ?>" data-name="<?php echo htmlspecialchars($act['name']); ?>"
                                                data-type="<?php echo $act['type']; ?>" data-date="<?php echo $act['date']; ?>"
                                                data-time="<?php echo $act['time']; ?>"
                                                data-notes="<?php echo htmlspecialchars($act['notes']); ?>">
                                                <i class="bi bi-pencil" style="color: var(--headline);"></i>
                                            </button>
                                            <a href="index.php?page=dashboard&delete=<?php echo $act['id']; ?>"
                                                class="btn btn-sm btn-light border shadow-sm rounded-circle text-danger"
                                                onclick="return confirm('Hapus aktivitas ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editActivityModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" style="color: var(--headline);">Edit Aktivitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?page=dashboard">
                <div class="modal-body p-4">
                    <input type="hidden" name="edit_activity" value="1">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NAMA</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">TIPE</label>
                        <select name="type" id="edit_type" class="form-select">
                            <option value="Outdoor">Outdoor</option>
                            <option value="Indoor">Indoor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">WAKTU</label>
                        <div class="input-group">
                            <input type="date" name="date" id="edit_date" class="form-control" required>
                            <input type="time" name="time" id="edit_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">CATATAN</label>
                        <textarea name="notes" id="edit_notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const editButtons = document.querySelectorAll('.btn-edit-activity');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_id').value = this.getAttribute('data-id');
                document.getElementById('edit_name').value = this.getAttribute('data-name');
                document.getElementById('edit_type').value = this.getAttribute('data-type');
                document.getElementById('edit_date').value = this.getAttribute('data-date');
                document.getElementById('edit_time').value = this.getAttribute('data-time');
                document.getElementById('edit_notes').value = this.getAttribute('data-notes');
            });
        });
    });
</script>

<?php include '../views/layout/footer.php'; ?>