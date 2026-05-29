<?php include 'app/views/shares/header.php'; ?>

<h1>Hóa đơn đã thanh toán</h1>

<?php if (empty($orders)): ?>
    <p>Chưa có hóa đơn nào.</p>
<?php else: ?>

    <?php foreach ($orders as $order): ?>
        <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
            <h3>Hóa đơn #<?php echo $order['id']; ?></h3>

            <p><strong>Khách hàng:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
            <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
            <p><strong>Ngày đặt:</strong> <?php echo $order['created_at']; ?></p>

            <table border="1" cellpadding="10" cellspacing="0" width="100%">
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Thành tiền</th>
                </tr>

                <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo number_format($item['price'], 0, ',', '.'); ?> đ</td>
                        <td><?php echo number_format($item['subtotal'], 0, ',', '.'); ?> đ</td>
                    </tr>
                <?php endforeach; ?>

                <tr>
                    <td colspan="3"><strong>Tổng tiền</strong></td>
                    <td>
                        <strong><?php echo number_format($order['total'], 0, ',', '.'); ?> đ</strong>
                    </td>
                </tr>
            </table>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

<?php include 'app/views/shares/footer.php'; ?>