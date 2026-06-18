<?php
require_once 'app/helpers/SessionHelper.php';
$isAdmin = SessionHelper::isAdmin();
include 'app/views/shares/header.php';
?>

<div class="api-page-header">
    <div>
        <h2 class="api-page-title">Quản lý sản phẩm bằng jQuery</h2>
        <p class="api-page-description">
            Danh sách được tải từ REST API bằng <code>$.ajax()</code>.
        </p>
    </div>

    <?php if ($isAdmin): ?>
        <a href="/webbanhang/Product/add" class="btn btn-duel-primary">
            + Thêm sản phẩm
        </a>
    <?php endif; ?>
</div>

<div id="productAlert"></div>

<div id="productGrid" class="api-product-grid">
    <div class="api-loading">
        <span class="api-spinner"></span>
        Đang tải sản phẩm...
    </div>
</div>

<script>
$(function () {
    const isAdmin = <?php echo json_encode($isAdmin); ?>;
    const $grid = $('#productGrid');
    const $alert = $('#productAlert');

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }

    function formatPrice(value) {
        return new Intl.NumberFormat('vi-VN').format(Number(value) || 0) + ' VNĐ';
    }

    function truncate(value, maxLength) {
        const text = String(value || '');
        return text.length > maxLength
            ? text.substring(0, maxLength) + '...'
            : text;
    }

    function showAlert(message, type) {
        $alert.html(
            '<div class="alert alert-' + (type || 'danger') + '">' + message + '</div>'
        );
    }

    function renderProducts(products) {
        if (!Array.isArray(products) || products.length === 0) {
            $grid.html(
                '<div class="api-empty">' +
                    '<h4>Chưa có sản phẩm</h4>' +
                    '<p class="mb-0">Danh sách hiện đang trống.</p>' +
                '</div>'
            );
            return;
        }

        let html = '';

        $.each(products, function (_, product) {
            const id = Number(product.id);
            const imageHtml = product.image
                ? '<img class="api-product-image" src="/webbanhang/' +
                    escapeHtml(product.image) + '" alt="' + escapeHtml(product.name) + '">'
                : '<div class="api-product-placeholder">Không có hình ảnh</div>';

            let actions =
                '<a class="btn btn-sm btn-info" href="/webbanhang/Product/show/' + id + '">Xem</a>';

            if (isAdmin) {
                actions +=
                    '<a class="btn btn-sm btn-warning" href="/webbanhang/Product/edit/' + id + '">Sửa</a>' +
                    '<button type="button" class="btn btn-sm btn-danger btn-delete-product" data-id="' + id + '">Xóa</button>';
            } else {
                actions +=
                    '<a class="btn btn-sm btn-success" href="/webbanhang/Product/addToCart/' + id + '">Thêm giỏ</a>';
            }

            html +=
                '<article class="api-product-card" id="product-' + id + '">' +
                    '<a class="api-product-image-wrap" href="/webbanhang/Product/show/' + id + '">' +
                        imageHtml +
                    '</a>' +
                    '<div class="api-product-name">' + escapeHtml(product.name) + '</div>' +
                    '<div class="api-product-category">' +
                        escapeHtml(product.category_name || 'Chưa có danh mục') +
                    '</div>' +
                    '<div class="api-product-description">' +
                        escapeHtml(truncate(product.description, 90)) +
                    '</div>' +
                    '<div class="api-product-price">' + formatPrice(product.price) + '</div>' +
                    '<div class="api-product-actions">' + actions + '</div>' +
                '</article>';
        });

        $grid.html(html);
    }

    function loadProducts() {
        $grid.html(
            '<div class="api-loading"><span class="api-spinner"></span>Đang tải sản phẩm...</div>'
        );

        ProductApi.getAll()
            .done(function (products) {
                renderProducts(products);
            })
            .fail(function (xhr) {
                $grid.html(
                    '<div class="api-empty text-danger">' +
                        escapeHtml(ProductApi.getErrorMessage(xhr, 'Không thể tải danh sách sản phẩm.')) +
                    '</div>'
                );
            });
    }

    $grid.on('click', '.btn-delete-product', function () {
        const id = $(this).data('id');
        const $button = $(this);

        if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
            return;
        }

        $button.prop('disabled', true).text('Đang xóa...');

        ProductApi.remove(id)
            .done(function (response) {
                showAlert(escapeHtml(response.message), 'success');
                $('#product-' + id).fadeOut(250, function () {
                    $(this).remove();

                    if ($grid.children('.api-product-card').length === 0) {
                        loadProducts();
                    }
                });
            })
            .fail(function (xhr) {
                showAlert(
                    escapeHtml(ProductApi.getErrorMessage(xhr, 'Xóa sản phẩm thất bại.')),
                    'danger'
                );
                $button.prop('disabled', false).text('Xóa');
            });
    });

    loadProducts();
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
