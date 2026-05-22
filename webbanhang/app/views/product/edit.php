<?php include 'app/views/shares/header.php'; ?>

<div class="duel-panel">

    <h2 class="duel-section-title mb-4">
        Sửa sản phẩm
    </h2>

    <?php if (!empty($errors)): ?>

        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li>
                        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

    <?php endif; ?>

    <form
        method="POST"
        action="/webbanhang/Product/update"
        enctype="multipart/form-data"
        onsubmit="return validateForm();">

        <input
            type="hidden"
            name="id"
            value="<?php echo $product->id; ?>">

        <div class="form-group">
            <label for="name">Tên sản phẩm:</label>

            <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                value="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>"
                required>
        </div>

        <div class="form-group">
            <label for="description">Mô tả:</label>

            <textarea
                id="description"
                name="description"
                class="form-control"
                rows="4"
                required><?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>

        <div class="form-group">
            <label for="price">Giá:</label>

            <input
                type="number"
                id="price"
                name="price"
                class="form-control"
                step="0.01"
                value="<?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?>"
                required>
        </div>

        <div class="form-group">
            <label for="category_id">Danh mục:</label>

            <select
                id="category_id"
                name="category_id"
                class="form-control"
                required>

                <?php foreach ($categories as $category): ?>

                    <option
                        value="<?php echo $category->id; ?>"
                        <?php echo $category->id == $product->category_id ? 'selected' : ''; ?>>

                        <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>

                    </option>

                <?php endforeach; ?>

            </select>
        </div>

        <div class="form-group">
            <label for="image">Hình ảnh:</label>

            <input
                type="file"
                id="image"
                name="image"
                class="form-control">

            <input
                type="hidden"
                name="existing_image"
                value="<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>">

            <?php if (!empty($product->image)): ?>

                <div class="mt-3">
                    <img
                        src="/webbanhang/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>"
                        alt="Product Image"
                        style="max-width: 160px; border-radius: 14px;">
                </div>

            <?php endif; ?>

        </div>

        <button type="submit" class="btn btn-duel-primary">
            Lưu thay đổi
        </button>

        <a href="/webbanhang/Product/list" class="btn btn-duel-secondary">
            Quay lại
        </a>

    </form>

</div>

<?php include 'app/views/shares/footer.php'; ?>