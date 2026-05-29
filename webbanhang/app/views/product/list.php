<?php include 'app/views/shares/header.php'; ?>

<style>
    .product-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 28px;
        flex-wrap: wrap;
    }

    .product-page-title {
        color: #ffffff;
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .product-page-desc {
        color: #cbd5e1;
        margin: 0;
        font-size: 15px;
    }

    .product-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 26px;
        align-items: flex-start;
    }

    .product-card {
        width: 235px;
        background: linear-gradient(180deg, #111827, #0f172a);
        border: 1px solid rgba(148, 163, 184, 0.35);
        border-radius: 16px;
        padding: 13px;
        box-sizing: border-box;
        box-shadow:
            0 10px 25px rgba(0, 0, 0, 0.38),
            inset 0 1px 0 rgba(255, 255, 255, 0.04);
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        overflow: hidden;
    }

    .product-card:hover {
        transform: translateY(-5px);
        border-color: rgba(56, 189, 248, 0.65);
        box-shadow:
            0 16px 34px rgba(0, 0, 0, 0.52),
            0 0 22px rgba(56, 189, 248, 0.12);
    }

    .product-card-name {
        margin-bottom: 10px;
        min-height: 42px;
    }

    .product-card-name a {
        color: #ffffff;
        font-size: 16px;
        font-weight: 800;
        text-decoration: none;
        line-height: 1.3;
        display: block;
    }

    .product-card-name a:hover {
        color: #38bdf8;
    }

    .product-card-image-wrap {
        width: 100%;
        aspect-ratio: 59 / 86;
        background: #020617;
        border: 1px solid rgba(148, 163, 184, 0.45);
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        margin-bottom: 13px;
        box-shadow: inset 0 0 18px rgba(0, 0, 0, 0.45);
    }

    .product-card-image {
        width: 100% !important;
        height: 100% !important;
        object-fit: contain !important;
        display: block;
        padding: 7px;
        box-sizing: border-box;
        background: #020617;
    }

    .product-card-placeholder {
        color: #94a3b8;
        font-size: 14px;
        text-align: center;
        padding: 10px;
    }

    .product-card-category {
        color: #facc15;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 8px;
        text-transform: uppercase;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-card-description {
        color: #d1d5db;
        font-size: 13px;
        line-height: 1.45;
        min-height: 40px;
        margin-bottom: 10px;
    }

    .product-card-price {
        color: #ffffff;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 12px;
    }

    .product-card-price span {
        color: #facc15;
    }

    .product-card-actions {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-top: 14px;
        margin-bottom: 10px;
    }

    .product-card-cart {
        margin-top: 8px;
    }

    .product-action-btn {
        border: none;
        outline: none;
        text-decoration: none !important;
        border-radius: 12px;
        padding: 9px 7px;
        font-size: 12px;
        font-weight: 800;
        text-align: center;
        color: #ffffff !important;
        transition: all 0.22s ease;
        box-shadow: 0 5px 14px rgba(0, 0, 0, 0.32);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        line-height: 1.2;
        position: relative;
        overflow: hidden;
    }

    .product-action-btn::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(
            120deg,
            transparent,
            rgba(255, 255, 255, 0.24),
            transparent
        );
        transform: translateX(-120%);
        transition: 0.45s ease;
    }

    .product-action-btn:hover::before {
        transform: translateX(120%);
    }

    .product-action-btn:hover {
        transform: translateY(-2px);
        color: #ffffff !important;
        box-shadow: 0 9px 22px rgba(0, 0, 0, 0.48);
    }

    .product-action-icon,
    .product-action-btn span {
        position: relative;
        z-index: 1;
    }

    .product-action-icon {
        font-size: 14px;
    }

    .product-action-view {
        background: linear-gradient(135deg, #2563eb, #38bdf8);
    }

    .product-action-edit {
        background: linear-gradient(135deg, #f59e0b, #facc15);
        color: #111827 !important;
    }

    .product-action-edit:hover {
        color: #111827 !important;
    }

    .product-action-delete {
        background: linear-gradient(135deg, #dc2626, #fb7185);
    }

    .product-action-cart {
        width: 100%;
        min-height: 42px;
        background: linear-gradient(135deg, #16a34a, #22c55e);
        border-radius: 14px;
        font-size: 13px;
        letter-spacing: 0.2px;
    }

    .empty-product-panel {
        width: 100%;
        background: #111827;
        border: 1px solid #374151;
        border-radius: 16px;
        padding: 34px;
        text-align: center;
        color: #ffffff;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
    }

    @media (max-width: 576px) {
        .product-card {
            width: 100%;
            max-width: 285px;
        }

        .product-grid {
            justify-content: center;
        }

        .product-page-title {
            font-size: 26px;
        }

        .product-card-actions {
            gap: 6px;
        }

        .product-action-btn {
            font-size: 11px;
            padding: 8px 5px;
        }

        .product-action-cart {
            font-size: 12px;
        }
    }
</style>

<div class="product-page-header">

    <div>
        <h2 class="product-page-title">
            Bộ sưu tập thẻ bài
        </h2>

        <p class="product-page-desc">
            Danh sách sản phẩm được hiển thị gọn gàng theo tỉ lệ thẻ bài Yu-Gi-Oh.
        </p>
    </div>

    <a href="/webbanhang/Product/add" class="btn btn-duel-primary">
        + Thêm thẻ mới
    </a>

</div>

<div class="product-grid">

    <?php if (!empty($products)): ?>

        <?php foreach ($products as $product): ?>

            <div class="product-card">

                <div class="product-card-name">
                    <a href="/webbanhang/Product/show/<?php echo $product->id; ?>">
                        <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </div>

                <a href="/webbanhang/Product/show/<?php echo $product->id; ?>" class="product-card-image-wrap">

                    <?php if (!empty($product->image)): ?>

                        <img
                            src="/webbanhang/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>"
                            alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>"
                            class="product-card-image">

                    <?php else: ?>

                        <div class="product-card-placeholder">
                            Không có hình ảnh
                        </div>

                    <?php endif; ?>

                </a>

                <div class="product-card-category">
                    [
                    <?php
                    echo !empty($product->category_name)
                        ? htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8')
                        : 'Chưa có danh mục';
                    ?>
                    ]
                </div>

                <div class="product-card-description">
                    <?php
                    $description = htmlspecialchars(
                        $product->description ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    );

                    echo mb_strlen($description, 'UTF-8') > 70
                        ? mb_substr($description, 0, 70, 'UTF-8') . '...'
                        : $description;
                    ?>
                </div>

                <div class="product-card-price">
                    Giá:
                    <span>
                        <?php echo number_format($product->price, 0, ',', '.'); ?> VNĐ
                    </span>
                </div>

                <div class="product-card-actions">

                    <a
                        href="/webbanhang/Product/show/<?php echo $product->id; ?>"
                        class="product-action-btn product-action-view">
                        <span class="product-action-icon">👁</span>
                        <span>Xem</span>
                    </a>

                    <a
                        href="/webbanhang/Product/edit/<?php echo $product->id; ?>"
                        class="product-action-btn product-action-edit">
                        <span class="product-action-icon">✎</span>
                        <span>Sửa</span>
                    </a>

                    <a
                        href="/webbanhang/Product/delete/<?php echo $product->id; ?>"
                        class="product-action-btn product-action-delete"
                        onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                        <span class="product-action-icon">✕</span>
                        <span>Xóa</span>
                    </a>

                </div>

                <div class="product-card-cart">
                    <a
                        href="/webbanhang/Product/addToCart/<?php echo $product->id; ?>"
                        class="product-action-btn product-action-cart">
                        <span class="product-action-icon">🛒</span>
                        <span>Thêm vào giỏ hàng</span>
                    </a>
                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="empty-product-panel">
            <h4>Chưa có thẻ bài nào</h4>

            <p class="text-muted">
                Hãy thêm sản phẩm đầu tiên vào shop thẻ của bạn.
            </p>

            <a href="/webbanhang/Product/add" class="btn btn-duel-primary">
                + Thêm thẻ mới
            </a>
        </div>

    <?php endif; ?>

</div>

<?php include 'app/views/shares/footer.php'; ?>