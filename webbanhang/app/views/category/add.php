<?php include 'app/views/shares/header.php'; ?>

<div class="duel-panel">

    <h2 class="duel-section-title mb-4">
        Thêm danh mục mới
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

    <form method="POST" action="/webbanhang/Category/save">

        <div class="form-group">
            <label for="name">Tên danh mục:</label>

            <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                value="<?php echo isset($name) ? htmlspecialchars($name, ENT_QUOTES, 'UTF-8') : ''; ?>"
                required>
        </div>

        <div class="form-group">
            <label for="description">Mô tả:</label>

            <textarea
                id="description"
                name="description"
                class="form-control"
                rows="4"><?php echo isset($description) ? htmlspecialchars($description, ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
        </div>

        <button type="submit" class="btn btn-duel-primary">
            Thêm danh mục
        </button>

        <a href="/webbanhang/Category/list" class="btn btn-duel-secondary">
            Quay lại
        </a>

    </form>

</div>

<?php include 'app/views/shares/footer.php'; ?>