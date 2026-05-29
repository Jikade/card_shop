<?php include 'app/views/shares/header.php'; ?>

<h1>Thanh toán</h1>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST" action="/webbanhang/Product/processCheckout">
    <label>Họ tên</label><br>
    <input type="text" name="name" required><br><br>

    <label>Số điện thoại</label><br>
    <input type="text" name="phone" required><br><br>

    <label>Địa chỉ</label><br>
    <textarea name="address" required></textarea><br><br>

    <button type="submit">Xác nhận đặt hàng</button>
</form>

<?php include 'app/views/shares/footer.php'; ?>