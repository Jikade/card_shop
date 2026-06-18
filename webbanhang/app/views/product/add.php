<?php
require_once 'app/helpers/SessionHelper.php';

if (!SessionHelper::isAdmin()) {
    http_response_code(403);
    die('Bạn không có quyền truy cập chức năng này!');
}

include 'app/views/shares/header.php';
?>

<div class="duel-panel api-form-panel">
    <h2 class="duel-section-title mb-4">Thêm sản phẩm bằng jQuery AJAX</h2>

    <div id="formAlert"></div>

    <form id="productForm" novalidate>
        <div class="form-group">
            <label for="name">Tên sản phẩm</label>
            <input type="text" id="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea id="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="price">Giá</label>
            <input type="number" id="price" class="form-control" min="0" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="category_id">Danh mục</label>
            <select id="category_id" class="form-control" required>
                <option value="">Đang tải danh mục...</option>
            </select>
        </div>

        <button type="submit" id="btnSubmit" class="btn btn-duel-primary">
            Thêm sản phẩm
        </button>

        <a href="/webbanhang/Product" class="btn btn-duel-secondary">
            Quay lại
        </a>
    </form>
</div>

<script>
$(function () {
    const $form = $('#productForm');
    const $category = $('#category_id');
    const $submit = $('#btnSubmit');
    const $alert = $('#formAlert');

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }

    function showAlert(message, type) {
        $alert.html(
            '<div class="alert alert-' + (type || 'danger') + '">' + message + '</div>'
        );
    }

    function loadCategories() {
        ProductApi.getCategories()
            .done(function (categories) {
                let options = '<option value="">-- Chọn danh mục --</option>';

                $.each(categories, function (_, category) {
                    options +=
                        '<option value="' + Number(category.id) + '">' +
                            escapeHtml(category.name) +
                        '</option>';
                });

                $category.html(options);
            })
            .fail(function (xhr) {
                $category.html('<option value="">Không tải được danh mục</option>');
                showAlert(
                    escapeHtml(ProductApi.getErrorMessage(xhr, 'Không thể tải danh mục.')),
                    'danger'
                );
            });
    }

    $form.on('submit', function (event) {
        event.preventDefault();
        $alert.empty();

        const data = {
            name: $.trim($('#name').val()),
            description: $.trim($('#description').val()),
            price: $('#price').val(),
            category_id: $category.val()
        };

        if (!data.name || !data.description || data.price === '' || !data.category_id) {
            showAlert('Vui lòng nhập đầy đủ thông tin sản phẩm.', 'danger');
            return;
        }

        $submit.prop('disabled', true).html('<span class="api-spinner"></span>Đang thêm...');

        ProductApi.create(data)
            .done(function (response) {
                showAlert(escapeHtml(response.message), 'success');

                setTimeout(function () {
                    window.location.href = '/webbanhang/Product';
                }, 500);
            })
            .fail(function (xhr) {
                showAlert(
                    ProductApi.getErrorMessage(xhr, 'Thêm sản phẩm thất bại.'),
                    'danger'
                );
            })
            .always(function () {
                $submit.prop('disabled', false).text('Thêm sản phẩm');
            });
    });

    loadCategories();
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
