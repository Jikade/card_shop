<?php include 'app/views/shares/header.php'; ?>

<?php if ($product): ?>

    <div class="duel-panel">

        <div class="row align-items-center">

            <div class="col-lg-6 mb-4 mb-lg-0">

                <?php if (!empty($product->image)): ?>

                    <img
                        src="/webbanhang/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>"
                        class="ygo-detail-card-image"
                        alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>">

                <?php else: ?>

                    <div class="duel-product-placeholder duel-detail-image">
                        Không có hình ảnh
                    </div>

                <?php endif; ?>

            </div>

            <div class="col-lg-6">

                <span class="duel-kicker">
                    Chi tiết sản phẩm
                </span>

                <h2 class="duel-section-title mb-4">
                    <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                </h2>

                <p class="text-muted">
                    <?php
                    echo nl2br(
                        htmlspecialchars(
                            $product->description,
                            ENT_QUOTES,
                            'UTF-8'
                        )
                    );
                    ?>
                </p>

                <p class="duel-price h3 my-4">
                    <?php echo number_format($product->price, 0, ',', '.'); ?> VND
                </p>

                <p>
                    <strong>Danh mục:</strong>

                    <span class="duel-badge">
                        <?php
                        echo !empty($product->category_name)
                            ? htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8')
                            : 'Chưa có danh mục';
                        ?>
                    </span>
                </p>

                <div class="mt-4">

                    <a
                        href="/webbanhang/Product/addToCart/<?php echo $product->id; ?>"
                        class="btn btn-duel-primary mr-2 mb-2">
                        + Thêm vào giỏ hàng
                    </a>

                    <a
                        href="/webbanhang/Product/edit/<?php echo $product->id; ?>"
                        class="btn btn-duel-warning mr-2 mb-2">
                        Sửa sản phẩm
                    </a>

                    <a
                        href="/webbanhang/Product/list"
                        class="btn btn-duel-secondary mb-2">
                        Quay lại
                    </a>

                </div>

            </div>

        </div>

    </div>

<?php else: ?>

    <div class="alert alert-danger text-center">
        <h4>Không tìm thấy sản phẩm!</h4>
    </div>

<?php endif; ?>

<?php include 'app/views/shares/footer.php'; ?>