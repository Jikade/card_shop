<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cartCount = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'] ?? 0;
    }
}
?>

<!DOCTYPE html>

<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duel Card Shop</title>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="/webbanhang/public/css/duel-theme.css">


</head>

<body>


<div class="falling-card-layer" id="fallingCardLayer"></div>

<nav class="navbar navbar-expand-lg navbar-dark duel-navbar">
    <div class="container">
        <a class="navbar-brand duel-brand" href="/webbanhang/Product">
            Duel Card Shop
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-toggle="collapse"
            data-target="#duelNavbar"
            aria-controls="duelNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="duelNavbar">
            <ul class="navbar-nav ml-auto align-items-lg-center">

                <li class="nav-item">
                    <a class="nav-link" href="/webbanhang/Product">
                        Sản phẩm
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/webbanhang/Product/add">
                        Thêm thẻ
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/webbanhang/Category/list">
                        Danh mục
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-cart-link" href="/webbanhang/Product/cart">
                        🛒 Giỏ hàng
                        <span class="cart-count">
                            <?php echo $cartCount; ?>
                        </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-invoice-link" href="/webbanhang/Product/invoices">
                        🧾 Hóa đơn
                    </a>
                </li>
                <?php if (isset($_SESSION['username'])): ?>

<li class="nav-item">
    <span class="nav-link">
        👋 Xin chào,
        <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
    </span>
</li>

<li class="nav-item">
    <a class="nav-link text-warning"
       href="/webbanhang/account/logout">
        🚪 Đăng xuất
    </a>
</li>

<?php else: ?>

<li class="nav-item">
    <a class="nav-link"
       href="/webbanhang/account/login">
        🔑 Đăng nhập
    </a>
</li>

<li class="nav-item">
    <a class="nav-link"
       href="/webbanhang/account/register">
        📝 Đăng ký
    </a>
</li>

<?php endif; ?>

            </ul>
        </div>
    </div>
</nav>

<header class="duel-hero">
    <div class="container">
        <span class="duel-kicker">
            Trading Card Store
        </span>

        <h1 class="duel-title">
            Thế giới thẻ bài
        </h1>

        <p class="duel-subtitle">
            Khám phá bộ sưu tập thẻ bài Yu-Gi-Oh!, Pokémon, Magic, One Piece và các phụ kiện dành cho người chơi.
        </p>
    </div>
</header>

<main class="duel-main">
    <div class="container">

