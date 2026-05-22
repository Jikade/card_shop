<?php include 'app/views/shares/header.php'; ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">

    <div>
        <h2 class="duel-section-title mb-2">
            Danh sách danh mục
        </h2>

        <p class="text-muted mb-0">
            Quản lý các danh mục thẻ bài trong cửa hàng.
        </p>
    </div>

    <a href="/webbanhang/Category/add" class="btn btn-duel-primary mt-3 mt-md-0">
        + Thêm danh mục
    </a>

</div>

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

<div class="duel-panel table-responsive">

    <table class="table table-hover mb-0">

        <thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th style="width: 200px;">Thao tác</th>
            </tr>
        </thead>

        <tbody>

            <?php if (!empty($categories)): ?>

                <?php foreach ($categories as $category): ?>

                    <tr>
                        <td>
                            <?php echo htmlspecialchars($category->id, ENT_QUOTES, 'UTF-8'); ?>
                        </td>

                        <td>
                            <strong>
                                <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                            </strong>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($category->description, ENT_QUOTES, 'UTF-8'); ?>
                        </td>

                        <td>
                            <a
                                href="/webbanhang/Category/edit/<?php echo $category->id; ?>"
                                class="btn btn-duel-warning btn-sm">
                                Sửa
                            </a>

                            <a
                                href="/webbanhang/Category/delete/<?php echo $category->id; ?>"
                                class="btn btn-duel-danger btn-sm"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                Xóa
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="4" class="text-center">
                        Chưa có danh mục nào.
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>

    </table>

</div>

<?php include 'app/views/shares/footer.php'; ?>