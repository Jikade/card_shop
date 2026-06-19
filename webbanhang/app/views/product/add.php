<?php
require_once 'app/helpers/AuthHelper.php';

if (!AuthHelper::isAdmin()) {
    http_response_code(403);
    die('Bạn không có quyền truy cập chức năng này!');
}

include 'app/views/shares/header.php';
?>

<div class="duel-panel api-form-panel">
    <h2 class="duel-section-title mb-4">Thêm sản phẩm bằng jQuery AJAX</h2>

    <div id="formAlert"></div>

    <form id="productForm" enctype="multipart/form-data" novalidate>
        <div class="form-group">
            <label for="name">Tên sản phẩm</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="price">Giá</label>
            <input type="number" id="price" name="price" class="form-control" min="0" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="category_id">Danh mục</label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">Đang tải danh mục...</option>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Ảnh sản phẩm</label>
            <input
                type="file"
                id="image"
                name="image"
                class="form-control-file"
                accept="image/jpeg,image/png,image/gif,image/webp"
            >
            <small class="form-text text-muted">
                Chỉ nhận JPG, JPEG, PNG, GIF, WEBP. Dung lượng tối đa 5MB.
            </small>

            <div id="imagePreviewWrap" class="mt-3" style="display:none;">
                <img
                    id="imagePreview"
                    src=""
                    alt="Xem trước ảnh sản phẩm"
                    style="max-width: 220px; max-height: 220px; object-fit: cover; border-radius: 12px; border: 1px solid rgba(255,255,255,.25);"
                >
            </div>
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
    const $image = $('#image');
    const $imagePreviewWrap = $('#imagePreviewWrap');
    const $imagePreview = $('#imagePreview');

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

    $image.on('change', function () {
        const file = this.files && this.files[0] ? this.files[0] : null;

        if (!file) {
            $imagePreviewWrap.hide();
            $imagePreview.attr('src', '');
            return;
        }

        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!allowedTypes.includes(file.type)) {
            showAlert('Chỉ cho phép upload ảnh JPG, JPEG, PNG, GIF hoặc WEBP.', 'danger');
            this.value = '';
            $imagePreviewWrap.hide();
            $imagePreview.attr('src', '');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            showAlert('File ảnh không được vượt quá 5MB.', 'danger');
            this.value = '';
            $imagePreviewWrap.hide();
            $imagePreview.attr('src', '');
            return;
        }

        $alert.empty();
        $imagePreview.attr('src', URL.createObjectURL(file));
        $imagePreviewWrap.show();
    });

    $form.on('submit', function (event) {
        event.preventDefault();
        $alert.empty();

        const name = $.trim($('#name').val());
        const description = $.trim($('#description').val());
        const price = $('#price').val();
        const categoryId = $category.val();

        if (!name || !description || price === '' || !categoryId) {
            showAlert('Vui lòng nhập đầy đủ thông tin sản phẩm.', 'danger');
            return;
        }

        const formData = new FormData(this);
        formData.set('name', name);
        formData.set('description', description);
        formData.set('price', price);
        formData.set('category_id', categoryId);

        $submit.prop('disabled', true).html('<span class="api-spinner"></span>Đang thêm...');

        ProductApi.createWithImage(formData)
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
