
    </div>
</main>

<footer class="duel-footer py-4 mt-5">
    <div class="container">
        <div class="row">

            <div class="col-md-6 mb-3 mb-md-0">
                <h5 class="duel-footer-title">
                    Duel Card Shop
                </h5>

                <p class="mb-0">
                    Website quản lý và bán thẻ bài dành cho người yêu thích card game.
                </p>
            </div>

            <div class="col-md-6 text-md-right">
                <p class="mb-1">
                    <a href="/webbanhang/Product">Sản phẩm</a>
                    |
                    <a href="/webbanhang/Product/cart">Giỏ hàng</a>
                    |
                    <a href="/webbanhang/Product/invoices">Hóa đơn</a>
                </p>

                <p class="mb-0">
                    © <?php echo date('Y'); ?> Duel Card Shop
                </p>
            </div>

        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const layer = document.getElementById('fallingCardLayer');

        if (!layer) {
            return;
        }

        const cardCount = window.innerWidth <= 576 ? 12 : 24;

        for (let i = 0; i < cardCount; i++) {
            const card = document.createElement('div');

            card.className = 'falling-card';

            const left = Math.random() * 100;
            const delay = Math.random() * 12;
            const duration = 9 + Math.random() * 10;
            const scale = 0.65 + Math.random() * 0.75;

            card.style.left = left + 'vw';
            card.style.animationDelay = '-' + delay + 's';
            card.style.animationDuration = duration + 's';
            card.style.transform = 'scale(' + scale + ')';

            layer.appendChild(card);
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
