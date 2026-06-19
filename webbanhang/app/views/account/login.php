<?php include 'app/views/shares/header.php'; ?>

<section class="vh-100 gradient-custom">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-dark text-white" style="border-radius: 1rem;">
                    <div class="card-body p-5 text-center">
                        <form id="loginForm" novalidate>
                            <div class="mb-md-5 mt-md-4 pb-5">
                                <h2 class="fw-bold mb-2 text-uppercase">Login</h2>

                                <p class="text-white-50 mb-4">
                                    Đăng nhập để nhận JWT và dùng các chức năng cần quyền.
                                </p>

                                <?php if (!empty($error)): ?>
                                    <div class="alert alert-danger">
                                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                <?php endif; ?>

                                <div id="loginAlert"></div>

                                <div class="form-outline form-white mb-4 text-left">
                                    <label class="form-label" for="username">UserName</label>
                                    <input
                                        type="text"
                                        id="username"
                                        name="username"
                                        class="form-control form-control-lg"
                                        required>
                                </div>

                                <div class="form-outline form-white mb-4 text-left">
                                    <label class="form-label" for="password">Password</label>
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control form-control-lg"
                                        required>
                                </div>

                                <button class="btn btn-outline-light btn-lg px-5" id="btnLogin" type="submit">
                                    Login
                                </button>
                            </div>

                            <div>
                                <p class="mb-0">
                                    Don't have an account?
                                    <a href="/webbanhang/account/register" class="text-white-50 fw-bold">Sign Up</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(function () {
    const $form = $('#loginForm');
    const $alert = $('#loginAlert');
    const $btnLogin = $('#btnLogin');

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }

    function showAlert(message, type) {
        $alert.html(
            '<div class="alert alert-' + (type || 'danger') + '">' + escapeHtml(message) + '</div>'
        );
    }

    $form.on('submit', function (event) {
        event.preventDefault();
        $alert.empty();

        const username = $.trim($('#username').val());
        const password = $('#password').val();

        if (!username || !password) {
            showAlert('Vui lòng nhập đầy đủ username và password.', 'danger');
            return;
        }

        $btnLogin.prop('disabled', true).text('Đang đăng nhập...');

        AuthApi.login(username, password)
            .done(function () {
                window.location.href = '/webbanhang/Product';
            })
            .fail(function (xhr) {
                showAlert(ProductApi.getErrorMessage(xhr, 'Đăng nhập thất bại.'), 'danger');
            })
            .always(function () {
                $btnLogin.prop('disabled', false).text('Login');
            });
    });
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
