<?php

require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');

class ProductController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);

        $this->startSession();
    }

    private function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        $products = $this->productModel->getProducts();

        include 'app/views/product/list.php';
    }

    public function list()
    {
        $products = $this->productModel->getProducts();

        include 'app/views/product/list.php';
    }

    public function show($id)
    {
        $product = $this->productModel->getProductById($id);

        if ($product) {
            include 'app/views/product/show.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    public function add()
    {
        $categories = (new CategoryModel($this->db))->getCategories();

        include_once 'app/views/product/add.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;

            try {
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $image = $this->uploadImage($_FILES['image']);
                } else {
                    $image = "";
                }

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
            } catch (Exception $e) {
                $errors = [$e->getMessage()];
                $categories = (new CategoryModel($this->db))->getCategories();

                include 'app/views/product/add.php';
            }
        }
    }

    public function edit($id)
    {
        $product = $this->productModel->getProductById($id);
        $categories = (new CategoryModel($this->db))->getCategories();

        if ($product) {
            include 'app/views/product/edit.php';
        } else {
            echo "Không thấy sản phẩm.";
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;

            try {
                if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                    $image = $this->uploadImage($_FILES['image']);
                } else {
                    $image = $_POST['existing_image'] ?? '';
                }

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
            } catch (Exception $e) {
                echo "Lỗi upload hình ảnh: " . $e->getMessage();
            }
        }
    }

    public function delete($id)
    {
        $result = $this->productModel->deleteProduct($id);

        if (is_array($result) && $result['success'] === true) {
            header('Location: /webbanhang/Product');
            exit;
        }

        if (is_array($result)) {
            echo "<div style='
                margin: 40px auto;
                max-width: 600px;
                padding: 20px;
                background: #111827;
                color: #ffffff;
                border: 1px solid #ef4444;
                border-radius: 12px;
                font-family: Arial, sans-serif;
                text-align: center;
            '>";

            echo "<h2 style='color:#ef4444;'>Không thể xóa sản phẩm</h2>";
            echo "<p>" . htmlspecialchars($result['message'], ENT_QUOTES, 'UTF-8') . "</p>";
            echo "<a href='/webbanhang/Product' style='
                display: inline-block;
                margin-top: 15px;
                padding: 10px 18px;
                background: #facc15;
                color: #111827;
                text-decoration: none;
                border-radius: 999px;
                font-weight: bold;
            '>Quay lại danh sách sản phẩm</a>";

            echo "</div>";
            return;
        }

        echo "Đã xảy ra lỗi khi xóa sản phẩm.";
    }

    private function uploadImage($file)
    {
        $target_dir = "uploads/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        $check = getimagesize($file["tmp_name"]);

        if ($check === false) {
            throw new Exception("File không phải là hình ảnh.");
        }

        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("Hình ảnh có kích thước quá lớn.");
        }

        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            throw new Exception("Chỉ cho phép các định dạng JPG, JPEG, PNG và GIF.");
        }

        $newFileName = uniqid('product_', true) . '.' . $imageFileType;
        $target_file = $target_dir . $newFileName;

        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
        }

        return $target_file;
    }

    public function addToCart($id)
    {
        $product = $this->productModel->getProductById($id);

        if (!$product) {
            echo "Không tìm thấy sản phẩm.";
            return;
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
                'quantity' => 1,
                'image' => $product->image
            ];
        }

        header('Location: /webbanhang/Product/cart');
        exit;
    }

    public function cart()
    {
        $cart = $_SESSION['cart'] ?? [];

        include 'app/views/product/cart.php';
    }

    public function increaseQuantity($id)
    {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        }

        header('Location: /webbanhang/Product/cart');
        exit;
    }

    public function decreaseQuantity($id)
    {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']--;

            if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        }

        header('Location: /webbanhang/Product/cart');
        exit;
    }

    public function removeFromCart($id)
    {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }

        header('Location: /webbanhang/Product/cart');
        exit;
    }

    public function checkout()
    {
        if (empty($_SESSION['cart'])) {
            header('Location: /webbanhang/Product/cart');
            exit;
        }

        include 'app/views/product/checkout.php';
    }

    public function processCheckout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /webbanhang/Product/checkout');
            exit;
        }

        if (empty($_SESSION['cart'])) {
            header('Location: /webbanhang/Product/cart');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($name === '' || $phone === '' || $address === '') {
            $error = "Vui lòng nhập đầy đủ thông tin đặt hàng.";
            include 'app/views/product/checkout.php';
            return;
        }

        try {
            $this->db->beginTransaction();

            $orderQuery = "
                INSERT INTO orders (name, phone, address)
                VALUES (:name, :phone, :address)
            ";

            $orderStmt = $this->db->prepare($orderQuery);
            $orderStmt->bindParam(':name', $name);
            $orderStmt->bindParam(':phone', $phone);
            $orderStmt->bindParam(':address', $address);
            $orderStmt->execute();

            $order_id = $this->db->lastInsertId();

            $detailQuery = "
                INSERT INTO order_details (order_id, product_id, quantity, price)
                VALUES (:order_id, :product_id, :quantity, :price)
            ";

            $detailStmt = $this->db->prepare($detailQuery);

            foreach ($_SESSION['cart'] as $product_id => $item) {
                $detailStmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
                $detailStmt->bindValue(':product_id', $item['id'], PDO::PARAM_INT);
                $detailStmt->bindValue(':quantity', $item['quantity'], PDO::PARAM_INT);
                $detailStmt->bindValue(':price', $item['price']);
                $detailStmt->execute();
            }

            $this->db->commit();

            unset($_SESSION['cart']);

            header('Location: /webbanhang/Product/orderConfirmation');
            exit;
        } catch (Exception $e) {
            $this->db->rollBack();

            echo "Đã xảy ra lỗi khi xử lý đơn hàng: " . $e->getMessage();
        }
    }

    public function orderConfirmation()
    {
        include 'app/views/product/orderConfirmation.php';
    }

    public function invoices()
    {
        $query = "
            SELECT 
                o.id AS order_id,
                o.name AS customer_name,
                o.phone,
                o.address,
                o.created_at,
                od.quantity,
                od.price,
                p.name AS product_name
            FROM orders o
            INNER JOIN order_details od ON o.id = od.order_id
            INNER JOIN product p ON od.product_id = p.id
            ORDER BY o.created_at DESC, o.id DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_OBJ);

        $orders = [];

        foreach ($rows as $row) {
            $orderId = $row->order_id;

            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    'id' => $row->order_id,
                    'customer_name' => $row->customer_name,
                    'phone' => $row->phone,
                    'address' => $row->address,
                    'created_at' => $row->created_at,
                    'items' => [],
                    'total' => 0
                ];
            }

            $subtotal = $row->quantity * $row->price;

            $orders[$orderId]['items'][] = [
                'product_name' => $row->product_name,
                'quantity' => $row->quantity,
                'price' => $row->price,
                'subtotal' => $subtotal
            ];

            $orders[$orderId]['total'] += $subtotal;
        }

        include 'app/views/product/invoices.php';
    }
}

?>