<?php
require_once 'app/helpers/SessionHelper.php';
$isAdmin = SessionHelper::isAdmin();
$productId = isset($product->id) ? (int) $product->id : 0;
include 'app/views/shares/header.php';
?>

<div id="productDetail" class="duel-panel">
    <div class="api-loading">
        <span class="api-spinner"></span>
        Đang tải thông tin sản phẩm...
    </div>
</div>

<script>
$(function () {
    const productId = <?php echo json_encode($productId); ?>;
    const isAdmin = <?php echo json_encode($isAdmin); ?>;
    const $detail = $('#productDetail');

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : value).html();
    }

    function formatPrice(value) {
        return new Intl.NumberFormat('vi-VN').format(Number(value) || 0) + ' VNĐ';
    }

    ProductApi.getOne(productId)
        .done(function (product) {
            const image = product.image
                ? '<img class="api-detail-image" src="/webbanhang/' +
                    escapeHtml(product.image) + '" alt="' + escapeHtml(product.name) + '">'
                : '<div class="api-product-placeholder api-detail-image">Không có hình ảnh</div>';

            let actions = '';

            if (isAdmin) {
                actions +=
                    '<a href="/webbanhang/Product/edit/' + Number(product.id) + '" ' +
                    'class="btn btn-warning mr-2 mb-2">Sửa sản phẩm</a>';
            } else {
                actions +=
                    '<a href="/webbanhang/Product/addToCart/' + Number(product.id) + '" ' +
                    'class="btn btn-success mr-2 mb-2">Thêm vào giỏ</a>';
            }

            actions +=
                '<a href="/webbanhang/Product" class="btn btn-duel-secondary mb-2">Quay lại</a>';

            $detail.html(
                '<div class="row align-items-center">' +
                    '<div class="col-lg-6 text-center mb-4 mb-lg-0">' + image + '</div>' +
                    '<div class="col-lg-6">' +
                        '<span class="duel-kicker">Chi tiết sản phẩm</span>' +
                        '<h2 class="duel-section-title mb-3">' + escapeHtml(product.name) + '</h2>' +
                        '<p class="text-muted" style="white-space: pre-line;">' +
                            escapeHtml(product.description) +
                        '</p>' +
                        '<p class="api-product-price h3">' + formatPrice(product.price) + '</p>' +
                        '<p><strong>Danh mục:</strong> ' +
                            '<span class="badge badge-warning">' +
                                escapeHtml(product.category_name || 'Chưa có danh mục') +
                            '</span>' +
                        '</p>' +
                        '<div class="mt-4">' + actions + '</div>' +
                    '</div>' +
                '</div>'
            );
        })
        .fail(function (xhr) {
            $detail.html(
                '<div class="alert alert-danger mb-0">' +
                    escapeHtml(ProductApi.getErrorMessage(xhr, 'Không thể tải sản phẩm.')) +
                '</div>'
            );
        });
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
