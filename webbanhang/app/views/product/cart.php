<?php include 'app/views/shares/header.php'; ?>

<h1>Giỏ hàng</h1>

<?php if (empty($cart)): ?>
    <p>Giỏ hàng đang trống.</p>
    <a href="/webbanhang/Product">Tiếp tục mua hàng</a>
<?php else: ?>

<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>Ảnh</th>
        <th>Sản phẩm</th>
        <th>Giá</th>
        <th>Số lượng</th>
        <th>Thành tiền</th>
        <th>Thao tác</th>
    </tr>

    <?php $total = 0; ?>

    <?php foreach ($cart as $id => $item): ?>
        <?php 
            $subtotal = $item['price'] * $item['quantity']; 
            $total += $subtotal;
        ?>

        <tr>
            <td>
                <?php if (!empty($item['image'])): ?>
                    <img src="/webbanhang/<?php echo htmlspecialchars($item['image']); ?>" width="80">
                <?php endif; ?>
            </td>

            <td><?php echo htmlspecialchars($item['name']); ?></td>

            <td>
                <?php echo number_format($item['price'], 0, ',', '.'); ?> đ
            </td>

            <td>
                <a href="/webbanhang/Product/decreaseQuantity/<?php echo $id; ?>">
                    <button type="button">-</button>
                </a>

                <strong style="margin: 0 10px;">
                    <?php echo $item['quantity']; ?>
                </strong>

                <a href="/webbanhang/Product/increaseQuantity/<?php echo $id; ?>">
                    <button type="button">+</button>
                </a>
            </td>

            <td>
                <?php echo number_format($subtotal, 0, ',', '.'); ?> đ
            </td>

            <td>
                <a href="/webbanhang/Product/removeFromCart/<?php echo $id; ?>"
                   onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?');">
                    Xóa
                </a>
            </td>
        </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="4"><strong>Tổng cộng</strong></td>
        <td colspan="2">
            <strong><?php echo number_format($total, 0, ',', '.'); ?> đ</strong>
        </td>
    </tr>
</table>

<br>

<a href="/webbanhang/Product">
    <button>Tiếp tục mua hàng</button>
</a>

<a href="/webbanhang/Product/checkout">
    <button>Thanh toán</button>
</a>

<?php endif; ?>

<?php include 'app/views/shares/footer.php'; ?>