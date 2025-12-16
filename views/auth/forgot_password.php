<?php include '../views/layout/header.php'; ?>

<style>
    body {
        display: block !important;
        overflow-y: auto;
        background-color: var(--bg-color);
    }

    .forgot-wrapper {
        min-height: calc(100vh - 140px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 0;
    }

    .glass-auth {
        background: #ffffff;
        border: 2px solid var(--tertiary);
        border-radius: 30px;
        padding: 3rem;
        box-shadow: 0 10px 40px rgba(39, 35, 67, 0.08);
        color: var(--headline);
        width: 100%;
        max-width: 450px;
    }

    .btn-login-custom {
        background: var(--button);
        color: var(--button-text);
        border-radius: 12px;
        font-weight: 700;
        padding: 0.8rem;
        border: 2px solid var(--button);
        box-shadow: 4px 4px 0 var(--headline);
        transition: 0.2s;
    }

    .btn-login-custom:hover {
        transform: translate(-2px, -2px);
        box-shadow: 6px 6px 0 var(--headline);
        background-color: #ffe033;
    }
</style>

<div class="forgot-wrapper">
    <div class="glass-auth animate__animated animate__zoomIn">
        <div class="text-center mb-5">
            <div class="bg-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm border border-dark" style="width: 70px; height: 70px;">
                <i class="bi bi-question-lg fs-1 text-dark"></i>
            </div>
            <h3 class="fw-bold mt-2">Lupa Password</h3>
            <p class="small opacity-75" style="color: var(--paragraph);">Masukkan email Anda untuk reset password.</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger py-2 border-0 text-center small mb-4 text-white" style="background: #ff5f5f;">
                <i class="bi bi-exclamation-circle me-1"></i> <?php echo $_SESSION['error'];
                                                                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success py-3 border-0 text-center small mb-4 fw-bold" style="background: #bae8e8; color: var(--headline);">
                <i class="bi bi-check-circle me-1"></i> <?php echo $_SESSION['success'];
                                                        unset($_SESSION['success']); ?>
                <p class="mt-2 mb-0 fw-normal">Silakan cek kotak masuk email Anda.</p>
            </div>
        <?php endif; ?>

        <form action="index.php?page=forgot_password" method="POST">
            <div class="mb-4">
                <label class="form-label small fw-bold text-uppercase ls-1" style="color: var(--paragraph);">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope-fill text-warning"></i></span>
                    <input type="email" class="form-control border-start-0 ps-0" name="email" placeholder="nama@email.com" required>
                </div>
            </div>

            <div class="d-grid mt-5">
                <button type="submit" class="btn btn-login-custom">
                    KIRIM LINK RESET <i class="bi bi-send ms-2"></i>
                </button>
            </div>
            <div class="text-center mt-3">
                <a href="index.php?page=login" class="small text-decoration-none fw-bold" style="color: var(--headline); opacity: 0.7;">&larr; Kembali ke Login</a>
            </div>
        </form>
    </div>
</div>

<?php include '../views/layout/footer.php'; ?>