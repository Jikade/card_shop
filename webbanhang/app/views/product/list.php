<?php include 'app/views/shares/header.php'; ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">

    <div>
        <h2 class="duel-section-title mb-2">
            Bộ sưu tập thẻ bài
        </h2>

        <p class="text-muted mb-0">
            Danh sách sản phẩm được hiển thị theo kích thước và tỉ lệ thẻ bài Yu-Gi-Oh.
        </p>
    </div>

    <a href="/webbanhang/Product/add" class="btn btn-duel-primary mt-3 mt-md-0">
        + Thêm thẻ mới
    </a>

</div>

<div class="ygo-card-grid">

    <?php if (!empty($products)): ?>

        <?php foreach ($products as $product): ?>

            <div class="ygo-card">

                <div class="ygo-card-inner">

                    <div class="ygo-card-name">
                        <a href="/webbanhang/Product/show/<?php echo $product->id; ?>">
                            <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </div>

                    <div class="ygo-card-stars">
                        ★ ★ ★ ★ ★ ★
                    </div>

                    <a
                        href="/webbanhang/Product/show/<?php echo $product->id; ?>"
                        class="ygo-card-image-wrap">

                        <?php if (!empty($product->image)): ?>

                            <img
                                src="/webbanhang/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>"
                                alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>"
                                class="ygo-card-image">

                        <?php else: ?>

                            <div class="ygo-card-placeholder">
                                Không có hình ảnh
                            </div>

                        <?php endif; ?>

                    </a>

                    <div class="ygo-card-type">
                        [
                        <?php
                        echo !empty($product->category_name)
                            ? htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8')
                            : 'Chưa có danh mục';
                        ?>
                        ]
                    </div>

                    <div class="ygo-card-description">
                        <?php
                        $description = htmlspecialchars(
                            $product->description,
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        echo mb_strlen($description, 'UTF-8') > 85
                            ? mb_substr($description, 0, 85, 'UTF-8') . '...'
                            : $description;
                        ?>
                    </div>

                    <div class="ygo-card-price">
                        GIÁ /
                        <?php echo number_format($product->price, 0, ',', '.'); ?>
                        VND
                    </div>

                    <div class="ygo-card-actions">

                        <a
                            href="/webbanhang/Product/show/<?php echo $product->id; ?>"
                            class="btn btn-dark btn-sm">
                            Xem
                        </a>

                        <a
                            href="/webbanhang/Product/edit/<?php echo $product->id; ?>"
                            class="btn btn-warning btn-sm">
                            Sửa
                        </a>

                        <a
                            href="/webbanhang/Product/delete/<?php echo $product->id; ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                            Xóa
                        </a>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="duel-panel text-center">
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