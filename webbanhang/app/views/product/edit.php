<?php
require_once 'app/helpers/AuthHelper.php';

if (!AuthHelper::isAdmin()) {
    http_response_code(403);
    die('Bạn không có quyền truy cập chức năng này!');
}

$productId = isset($product->id) ? (int) $product->id : 0;
include 'app/views/shares/header.php';
?>

<div class="duel-panel api-form-panel">
    <h2 class="duel-section-title mb-4">Cập nhật sản phẩm bằng jQuery AJAX</h2>

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
                <option value="">Đang tải dữ liệu...</option>
            </select>
        </div>

        <div class="form-group">
            <label>Ảnh hiện tại</label>
            <div id="currentImageWrap" class="mt-2" style="display:none;">
                <img
                    id="currentImage"
                    src=""
                    alt="Ảnh hiện tại của sản phẩm"
                    style="max-width: 220px; max-height: 220px; object-fit: cover; border-radius: 12px; border: 1px solid rgba(255,255,255,.25);"
                >
            </div>
            <div id="noCurrentImage" class="text-muted mt-2">
                Sản phẩm hiện chưa có ảnh.
            </div>
        </div>

        <div class="form-group">
            <label for="image">Đổi ảnh sản phẩm</label>
            <input
                type="file"
                id="image"
                name="image"
                class="form-control-file"
                accept="image/jpeg,image/png,image/gif,image/webp"
            >
            <small class="form-text text-muted">
                Bỏ trống nếu muốn giữ ảnh cũ. Chỉ nhận JPG, JPEG, PNG, GIF, WEBP. Dung lượng tối đa 5MB.
            </small>

            <div id="newImagePreviewWrap" class="mt-3" style="display:none;">
                <label class="d-block">Ảnh mới sẽ thay thế</label>
                <img
                    id="newImagePreview"
                    src=""
                    alt="Xem trước ảnh mới"
                    style="max-width: 220px; max-height: 220px; object-fit: cover; border-radius: 12px; border: 1px solid rgba(255,255,255,.25);"
                >
            </div>
        </div>

        <button type="submit" id="btnSubmit" class="btn btn-duel-primary">
            Lưu thay đổi
        </button>

        <a href="/webbanhang/Product" class="btn btn-duel-secondary">
            Quay lại
        </a>
    </form>
</div>

<script>
$(function () {
    const productId = <?php echo json_encode($productId); ?>;
    const $form = $('#productForm');
    const $category = $('#category_id');
    const $submit = $('#btnSubmit');
    const $alert = $('#formAlert');
    const $image = $('#image');
    const maxImageSize = 5 * 1024 * 1024;
    const allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }

    function showAlert(message, type) {
        $alert.html(
            '<div class="alert alert-' + (type || 'danger') + '">' + message + '</div>'
        );
    }

    function getImageUrl(imagePath) {
        if (!imagePath) {
            return '';
        }

        if (/^https?:\/\//i.test(imagePath)) {
            return imagePath;
        }

        return '/webbanhang/' + imagePath.replace(/^\/+/, '');
    }

    function showCurrentImage(imagePath) {
        const imageUrl = getImageUrl(imagePath);

        if (!imageUrl) {
            $('#currentImageWrap').hide();
            $('#noCurrentImage').show();
            return;
        }

        $('#currentImage').attr('src', imageUrl);
        $('#currentImageWrap').show();
        $('#noCurrentImage').hide();
    }

    function resetNewImagePreview() {
        $('#newImagePreview').attr('src', '');
        $('#newImagePreviewWrap').hide();
    }

    function loadFormData() {
        if (!productId) {
            showAlert('ID sản phẩm không hợp lệ.', 'danger');
            $submit.prop('disabled', true);
            return;
        }

        $.when(
            ProductApi.getOne(productId),
            ProductApi.getCategories()
        )
        .done(function (productResponse, categoryResponse) {
            const product = productResponse[0];
            const categories = categoryResponse[0];

            $('#name').val(product.name || '');
            $('#description').val(product.description || '');
            $('#price').val(product.price || '');
            showCurrentImage(product.image || '');

            let options = '<option value="">-- Chọn danh mục --</option>';

            $.each(categories, function (_, category) {
                const selected = String(category.id) === String(product.category_id)
                    ? ' selected'
                    : '';

                options +=
                    '<option value="' + Number(category.id) + '"' + selected + '>' +
                        escapeHtml(category.name) +
                    '</option>';
            });

            $category.html(options);
        })
        .fail(function (xhr) {
            showAlert(
                escapeHtml(ProductApi.getErrorMessage(xhr, 'Không thể tải dữ liệu sản phẩm.')),
                'danger'
            );
            $submit.prop('disabled', true);
        });
    }

    $image.on('change', function () {
        $alert.empty();
        resetNewImagePreview();

        const file = this.files && this.files[0] ? this.files[0] : null;

        if (!file) {
            return;
        }

        if (!allowedImageTypes.includes(file.type)) {
            this.value = '';
            showAlert('Chỉ cho phép chọn ảnh JPG, JPEG, PNG, GIF hoặc WEBP.', 'danger');
            return;
        }

        if (file.size > maxImageSize) {
            this.value = '';
            showAlert('File ảnh không được vượt quá 5MB.', 'danger');
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            $('#newImagePreview').attr('src', event.target.result);
            $('#newImagePreviewWrap').show();
        };

        reader.readAsDataURL(file);
    });

    $form.on('submit', function (event) {
        event.preventDefault();
        $alert.empty();

        const name = $.trim($('#name').val());
        const description = $.trim($('#description').val());
        const price = $('#price').val();
        const categoryId = $category.val();
        const file = $image[0].files && $image[0].files[0] ? $image[0].files[0] : null;

        if (!name || !description || price === '' || !categoryId) {
            showAlert('Vui lòng nhập đầy đủ thông tin sản phẩm.', 'danger');
            return;
        }

        if (file && !allowedImageTypes.includes(file.type)) {
            showAlert('Chỉ cho phép chọn ảnh JPG, JPEG, PNG, GIF hoặc WEBP.', 'danger');
            return;
        }

        if (file && file.size > maxImageSize) {
            showAlert('File ảnh không được vượt quá 5MB.', 'danger');
            return;
        }

        const formData = new FormData(this);
        formData.set('name', name);
        formData.set('description', description);
        formData.set('price', price);
        formData.set('category_id', categoryId);

        $submit.prop('disabled', true).html('<span class="api-spinner"></span>Đang lưu...');

        ProductApi.updateWithImage(productId, formData)
            .done(function (response) {
                showAlert(escapeHtml(response.message), 'success');

                setTimeout(function () {
                    window.location.href = '/webbanhang/Product';
                }, 500);
            })
            .fail(function (xhr) {
                showAlert(
                    ProductApi.getErrorMessage(xhr, 'Cập nhật sản phẩm thất bại.'),
                    'danger'
                );
            })
            .always(function () {
                $submit.prop('disabled', false).text('Lưu thay đổi');
            });
    });

    loadFormData();
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
