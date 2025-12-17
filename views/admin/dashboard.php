<?php include '../views/layout/header.php'; ?>

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock-fill text-warning me-2"></i> Dashboard Admin</h2>
            <p class="text-muted">Pusat kontrol sistem dan manajemen pengguna.</p>
        </div>

        <div class="d-flex gap-2">
            <div class="btn-group">
                <button type="button" class="btn btn-success rounded-pill shadow-sm fw-bold px-4" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-2"></i> Laporan
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow p-2 glass-card">
                    <li><a class="dropdown-item rounded" href="index.php?page=admin_dashboard&export=activities"><i class="bi bi-file-earmark-spreadsheet me-2 text-success"></i> CSV Aktivitas</a></li>
                    <li><a class="dropdown-item rounded" href="index.php?page=admin_dashboard&export=notifications"><i class="bi bi-bell me-2 text-warning"></i> CSV Notifikasi</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success glass-card border-0 shadow-sm mb-4 d-flex align-items-center" style="background-color: #d1e7dd !important; color: #0f5132;">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>
                <?php
                if ($_GET['msg'] == 'created') echo "Member baru berhasil ditambahkan.";
                if ($_GET['msg'] == 'updated') echo "Data member berhasil diperbarui.";
                if ($_GET['msg'] == 'deleted') echo "Member berhasil dihapus.";
                ?>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger glass-card border-0 shadow-sm mb-4 d-flex align-items-center" style="background-color: #f8d7da !important; color: #842029;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                <?php
                if ($_GET['error'] == 'email_exists') echo "Gagal menambah user. Email sudah terdaftar dalam sistem!";
                elseif ($_GET['error'] == 'invalid_input') echo "Gagal menambah user. Pastikan semua kolom terisi dan password minimal 6 karakter.";
                elseif ($_GET['error'] == 'create_failed') echo "Gagal menambah user. Terjadi kesalahan pada database.";
                elseif ($_GET['error'] == 'self_delete') echo "Anda tidak dapat menghapus akun Anda sendiri.";
                else echo "Terjadi kesalahan saat memproses permintaan.";
                ?>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Kartu Grafik Statistik -->
        <div class="col-lg-4">
            <div class="glass-card h-100 d-flex flex-column">
                <div class="mb-4 mt-3 ms-2">
                    <h5 class="fw-bold m-0" style="color: var(--headline);"><i class="bi bi-pie-chart-fill me-2 text-warning"></i>Statistik Aktivitas</h5>
                </div>
                <div class="flex-grow-1 d-flex align-items-center justify-content-center position-relative">
                    <div style="width: 100%; max-width: 280px; min-height: 280px;">
                        <canvas id="typeChart"></canvas>
                    </div>
                    <div class="position-absolute top-50 start-50 translate-middle text-center text-dark">
                        <div class="h3 fw-bold mb-0"><?php echo array_sum($pieData); ?></div>
                        <div class="small text-muted">Total</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Manajemen User -->
        <div class="col-lg-8">
            <div class="glass-card h-100 p-0 overflow-hidden">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center" style="background-color: var(--secondary); border-color: var(--tertiary) !important;">
                    <h5 class="fw-bold m-0" style="color: var(--headline);"><i class="bi bi-people-fill me-2 text-primary"></i>Manajemen Pengguna</h5>
                    <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus-fill me-2"></i> Tambah User
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background-color: #fff;">
                            <tr>
                                <th class="ps-4 py-3 text-muted small fw-bold text-uppercase">User Info</th>
                                <th class="text-muted small fw-bold text-uppercase">Role</th>
                                <th class="text-end pe-4 text-muted small fw-bold text-uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-transparent">
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td class="ps-4 py-3 text-dark">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning bg-opacity-25 rounded-circle p-2 me-3 text-dark border border-warning">
                                                <i class="bi bi-person-fill fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold" style="color: var(--headline);"><?php echo htmlspecialchars($u['username']); ?></div>
                                                <div class="small text-muted"><?php echo htmlspecialchars($u['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($u['role'] == 'administrator'): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3">
                                                Administrator
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3">
                                                Guest User
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <!-- Tombol Edit -->
                                        <button class="btn btn-sm btn-light border shadow-sm rounded-circle me-1 btn-edit-user"
                                            title="Edit User"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editUserModal"
                                            data-id="<?php echo $u['id']; ?>"
                                            data-username="<?php echo htmlspecialchars($u['username']); ?>"
                                            data-email="<?php echo htmlspecialchars($u['email']); ?>">
                                            <i class="bi bi-pencil text-dark"></i>
                                        </button>

                                        <?php if ($u['role'] !== 'administrator' || $u['id'] != $_SESSION['user']['id']): ?>
                                            <?php if ($u['id'] != $_SESSION['user']['id']): ?>
                                                <!-- Tombol Hapus -->
                                                <a href="index.php?page=admin_dashboard&delete_user=<?php echo $u['id']; ?>"
                                                    class="btn btn-sm btn-light border shadow-sm rounded-circle text-danger"
                                                    title="Hapus User"
                                                    onclick="return confirm('Hapus user <?php echo $u['username']; ?>?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH USER -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title text-dark fw-bold">Tambah Member Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?page=admin_dashboard">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="create_user">
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">USERNAME</label>
                        <input type="text" name="username" class="form-control bg-light text-dark border" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">EMAIL</label>
                        <input type="email" name="email" class="form-control bg-light text-dark border" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">ROLE</label>
                        <select name="role" class="form-select bg-light text-dark border">
                            <option value="guest user">Guest User</option>
                            <option value="administrator">Administrator</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">PASSWORD</label>
                        <input type="password" name="password" class="form-control bg-light text-dark border" required>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT USER -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title text-dark fw-bold">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?page=admin_dashboard">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="edit_user">
                    <input type="hidden" name="user_id" id="edit_user_id">

                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">USERNAME</label>
                        <input type="text" name="username" id="edit_username" class="form-control bg-light text-dark border" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">EMAIL</label>
                        <input type="email" name="email" id="edit_email" class="form-control bg-light text-dark border" required>
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

<!-- Script Chart & Modal Logic -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('typeChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($pieLabels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($pieData); ?>,
                        backgroundColor: ['#ffd803', '#272343'],
                        borderWidth: 0,
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#333',
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    family: 'Poppins'
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // 2. Logic Modal Edit User
        const editButtons = document.querySelectorAll('.btn-edit-user');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_user_id').value = this.getAttribute('data-id');
                document.getElementById('edit_username').value = this.getAttribute('data-username');
                document.getElementById('edit_email').value = this.getAttribute('data-email');
            });
        });
    });
</script>

<?php include '../views/layout/footer.php'; ?>