<?php

require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/helpers/AuthHelper.php';

class ProductController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    // Kiểm tra quyền Admin
    private function isAdmin()
    {
        return AuthHelper::isAdmin();
    }

    // Hiển thị danh sách sản phẩm
    public function index()
    {
        $products = $this->productModel->getProducts();

        include 'app/views/product/list.php';
    }

    // Xem chi tiết sản phẩm
    public function show($id)
    {
        $product = $this->productModel->getProductById($id);

        if ($product) {
            include 'app/views/product/show.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    // Thêm sản phẩm, chỉ Admin
    public function add()
    {
        AuthHelper::requireAdmin(false);

        $categories = (new CategoryModel($this->db))->getCategories();

        include_once 'app/views/product/add.php';
    }

    // Lưu sản phẩm mới, chỉ Admin
    public function save()
    {
        AuthHelper::requireAdmin(false);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;

            $image = (
                isset($_FILES['image']) &&
                $_FILES['image']['error'] == 0
            )
                ? $this->uploadImage($_FILES['image'])
                : "";

            $result = $this->productModel->addProduct(
                $name,
                $description,
                $price,
                $category_id,
                $image
            );

            if (is_array($result)) {

                $errors = $result;
                $categories = (new CategoryModel($this->db))->getCategories();

                include 'app/views/product/add.php';

            } else {

                header('Location: /webbanhang/Product');
                exit;

            }
        }
    }

    // Sửa sản phẩm, chỉ Admin
    public function edit($id)
    {
        AuthHelper::requireAdmin(false);

        $product = $this->productModel->getProductById($id);
        $categories = (new CategoryModel($this->db))->getCategories();

        if ($product) {
            include 'app/views/product/edit.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    // Cập nhật sản phẩm, chỉ Admin
    public function update()
    {
        AuthHelper::requireAdmin(false);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id = $_POST['id'] ?? '';
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;

            $image = (
                isset($_FILES['image']) &&
                $_FILES['image']['error'] == 0
            )
                ? $this->uploadImage($_FILES['image'])
                : ($_POST['existing_image'] ?? '');

            $edit = $this->productModel->updateProduct(
                $id,
                $name,
                $description,
                $price,
                $category_id,
                $image
            );

            if ($edit) {
                header('Location: /webbanhang/Product');
                exit;
            } else {
                echo "Đã xảy ra lỗi khi lưu sản phẩm.";
            }
        }
    }

    // Xóa sản phẩm, chỉ Admin
    public function delete($id)
    {
        AuthHelper::requireAdmin(false);

        $result = $this->productModel->deleteProduct($id);

        if (is_array($result)) {
            if ($result['success']) {
                header('Location: /webbanhang/Product');
                exit;
            }

            echo $result['message'];
            exit;
        }

        if ($result) {
            header('Location: /webbanhang/Product');
            exit;
        }

        echo "Đã xảy ra lỗi khi xóa sản phẩm.";
    }

    // Thêm sản phẩm vào giỏ hàng
    public function addToCart($id)
    {
        AuthHelper::requireLogin(false);

        $product = $this->productModel->getProductById($id);

        if (!$product) {
            echo "Không tìm thấy sản phẩm.";
            exit;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => 1
            ];
        }

        header('Location: /webbanhang/Product/cart');
        exit;
    }

    // Hiển thị giỏ hàng
    public function cart()
    {
        AuthHelper::requireLogin(false);

        $cart = $_SESSION['cart'] ?? [];

        include 'app/views/product/cart.php';
    }

    // Tăng số lượng sản phẩm trong giỏ
    public function increaseQuantity($id)
    {
        AuthHelper::requireLogin(false);

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        }

        header('Location: /webbanhang/Product/cart');
        exit;
    }

    // Giảm số lượng sản phẩm trong giỏ
    public function decreaseQuantity($id)
    {
        AuthHelper::requireLogin(false);

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']--;

            if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        }

        header('Location: /webbanhang/Product/cart');
        exit;
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function removeFromCart($id)
    {
        AuthHelper::requireLogin(false);

        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }

        header('Location: /webbanhang/Product/cart');
        exit;
    }

    // Hiển thị trang thanh toán
    public function checkout()
    {
        AuthHelper::requireLogin(false);

        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            header('Location: /webbanhang/Product/cart');
            exit;
        }

        include 'app/views/product/checkout.php';
    }

    // Xử lý thanh toán
    public function processCheckout()
    {
        AuthHelper::requireLogin(false);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /webbanhang/Product/cart');
            exit;
        }

        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            header('Location: /webbanhang/Product/cart');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($name === '' || $phone === '' || $address === '') {
            $error = "Vui lòng nhập đầy đủ thông tin thanh toán.";
            include 'app/views/product/checkout.php';
            return;
        }

        try {
            $this->db->beginTransaction();

            $query = "
                INSERT INTO orders (name, phone, address)
                VALUES (:name, :phone, :address)
            ";

            $stmt = $this->db->prepare($query);

            $stmt->bindValue(':name', htmlspecialchars(strip_tags($name)));
            $stmt->bindValue(':phone', htmlspecialchars(strip_tags($phone)));
            $stmt->bindValue(':address', htmlspecialchars(strip_tags($address)));

            $stmt->execute();

            $orderId = $this->db->lastInsertId();

            $detailQuery = "
                INSERT INTO order_details (order_id, product_id, quantity, price)
                VALUES (:order_id, :product_id, :quantity, :price)
            ";

            $detailStmt = $this->db->prepare($detailQuery);

            foreach ($cart as $item) {
                $detailStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
                $detailStmt->bindValue(':product_id', $item['id'], PDO::PARAM_INT);
                $detailStmt->bindValue(':quantity', $item['quantity'], PDO::PARAM_INT);
                $detailStmt->bindValue(':price', $item['price']);
                $detailStmt->execute();
            }

            $this->db->commit();

            unset($_SESSION['cart']);

            include 'app/views/product/orderConfirmation.php';

        } catch (PDOException $e) {
            $this->db->rollBack();

            $error = "Đã xảy ra lỗi khi đặt hàng: " . $e->getMessage();

            include 'app/views/product/checkout.php';
        }
    }

    // Hiển thị danh sách hóa đơn
    public function invoices()
    {
        AuthHelper::requireLogin(false);

        $query = "
            SELECT 
                o.id AS order_id,
                o.name AS customer_name,
                o.phone,
                o.address,
                o.created_at,
                od.product_id,
                od.quantity,
                od.price,
                p.name AS product_name
            FROM orders o
            LEFT JOIN order_details od
                ON o.id = od.order_id
            LEFT JOIN product p
                ON od.product_id = p.id
            ORDER BY o.created_at DESC, o.id DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $orders = [];

        foreach ($rows as $row) {
            $orderId = $row['order_id'];

            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    'id' => $orderId,
                    'customer_name' => $row['customer_name'],
                    'phone' => $row['phone'],
                    'address' => $row['address'],
                    'created_at' => $row['created_at'],
                    'items' => [],
                    'total' => 0
                ];
            }

            if (!empty($row['product_id'])) {
                $subtotal = $row['quantity'] * $row['price'];

                $orders[$orderId]['items'][] = [
                    'product_id' => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'quantity' => $row['quantity'],
                    'price' => $row['price'],
                    'subtotal' => $subtotal
                ];

                $orders[$orderId]['total'] += $subtotal;
            }
        }

        include 'app/views/product/invoices.php';
    }

    // Upload ảnh sản phẩm
    private function uploadImage($file)
    {
        $targetDir = "uploads/";

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (!isset($file["name"]) || $file["name"] === "") {
            return "";
        }

        if ($file["error"] !== UPLOAD_ERR_OK) {
            echo "Có lỗi xảy ra khi upload ảnh.";
            exit;
        }

        $fileName = basename($file["name"]);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ["jpg", "jpeg", "png", "gif", "webp"];

        if (!in_array($fileExtension, $allowedExtensions)) {
            echo "Chỉ cho phép upload file ảnh: JPG, JPEG, PNG, GIF, WEBP.";
            exit;
        }

        if ($file["size"] > 5 * 1024 * 1024) {
            echo "File ảnh không được vượt quá 5MB.";
            exit;
        }

        $newFileName = uniqid("product_", true) . "." . $fileExtension;
        $targetFile = $targetDir . $newFileName;

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $targetFile;
        }

        echo "Có lỗi xảy ra khi upload ảnh.";
        exit;
    }
}