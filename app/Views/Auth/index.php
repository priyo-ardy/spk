<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>.:: User Authentication ::.</title>
    <!-- Favicon -->
    <link rel="icon" href="<?= base_url('image/favicon.ico') ?>" type="image/x-icon">
    <link rel="icon" href="<?= base_url('image/favicon.ico') ?>" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="<?= base_url('image/favicon.ico') ?>" sizes="180x180">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Ubuntu Font -->
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500&display=swap" rel="stylesheet">
    <!-- Auth CSS -->
    <link href="<?= base_url() . 'css/Auth/auth.css' ?>" rel="stylesheet">
</head>

<body>
    <div class="login-container floating">
        <div class="login-header">
            <img src="<?= base_url() . 'image/panjang.png' ?>" alt="Schlemmer Logo" class="company-logo">
            <div class="logo-line"></div>
        </div>

        <form id="formAuth">
            <div class="mb-3">
                <input type="text" class="form-control" name="username" id="username" placeholder="User name" autofocus autocomplete="off">
                <div class="invalid-feedback">Username is required</div>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" autocomplete="off">
                <div class="invalid-feedback">Password is required</div>
            </div>

            <div class="forgot-password">
                <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot password?</a>
            </div>

            <button type="button" id="btnAuth" class="btn btn-login"><i class="bi bi-box-arrow-in-right"></i>&ensp;Log in</button>
        </form>

        <div class="login-footer">
            <div>© 2024 <?= $app_ver ?></div>
            <div><?= $app_name ?></div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade rounded-0" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Please enter your email address to receive a password reset link:</p>
                    <div class="mb-3">
                        <input type="email" id="resetEmail" class="form-control" id="resetEmail" placeholder="Email address">
                        <div class="invalid-feedback">Email address is required</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnCancel" class="btn rounded-0 btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="btnReset" class="btn rounded-0 btn-modal">Send Reset Link</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Sweetalert2 plugins -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Authjavascript -->
    <script src="<?= base_url() . 'js/Auth/auth.js' ?>"></script>
</body>

</html>