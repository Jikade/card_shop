<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/helpers/AuthHelper.php';

class ProductApiController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    public function index()
    {
        header('Content-Type: application/json; charset=UTF-8');

        // Cho khách xem danh sách sản phẩm. JWT chỉ bắt buộc với thao tác cần quyền.
        $products = $this->productModel->getProducts();
        echo json_encode($products, JSON_UNESCAPED_UNICODE);
    }

    public function show($id)
    {
        header('Content-Type: application/json; charset=UTF-8');

        $product = $this->productModel->getProductById($id);

        if ($product) {
            echo json_encode($product, JSON_UNESCAPED_UNICODE);
            return;
        }

        http_response_code(404);
        echo json_encode(['message' => 'Không tìm thấy sản phẩm.'], JSON_UNESCAPED_UNICODE);
    }

    public function store()
    {
        AuthHelper::requireAdmin(true);
        header('Content-Type: application/json; charset=UTF-8');

        $data = $this->getRequestData();
        $image = $data['image'] ?? null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->uploadImage($_FILES['image']);

            if (isset($uploadResult['error'])) {
                http_response_code(400);
                echo json_encode(['message' => $uploadResult['error']], JSON_UNESCAPED_UNICODE);
                return;
            }

            $image = $uploadResult['path'];
        }

        $result = $this->productModel->addProduct(
            $data['name'] ?? '',
            $data['description'] ?? '',
            $data['price'] ?? '',
            $data['category_id'] ?? null,
            $image
        );

        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result], JSON_UNESCAPED_UNICODE);
            return;
        }

        http_response_code(201);
        echo json_encode(['message' => 'Thêm sản phẩm thành công.'], JSON_UNESCAPED_UNICODE);
    }

    public function update($id)
    {
        AuthHelper::requireAdmin(true);
        header('Content-Type: application/json; charset=UTF-8');

        $data = $this->getRequestData();
        $image = $data['image'] ?? null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->uploadImage($_FILES['image']);

            if (isset($uploadResult['error'])) {
                http_response_code(400);
                echo json_encode(['message' => $uploadResult['error']], JSON_UNESCAPED_UNICODE);
                return;
            }

            $image = $uploadResult['path'];
        }

        $result = $this->productModel->updateProduct(
            $id,
            $data['name'] ?? '',
            $data['description'] ?? '',
            $data['price'] ?? '',
            $data['category_id'] ?? null,
            $image
        );

        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result], JSON_UNESCAPED_UNICODE);
            return;
        }

        if ($result) {
            echo json_encode(['message' => 'Cập nhật sản phẩm thành công.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        http_response_code(400);
        echo json_encode(['message' => 'Cập nhật sản phẩm thất bại.'], JSON_UNESCAPED_UNICODE);
    }

    public function destroy($id)
    {
        AuthHelper::requireAdmin(true);
        header('Content-Type: application/json; charset=UTF-8');

        $result = $this->productModel->deleteProduct($id);

        if (is_array($result)) {
            http_response_code($result['success'] ?? false ? 200 : 400);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return;
        }

        if ($result) {
            echo json_encode(['message' => 'Xóa sản phẩm thành công.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        http_response_code(400);
        echo json_encode(['message' => 'Xóa sản phẩm thất bại.'], JSON_UNESCAPED_UNICODE);
    }

    private function getRequestData(): array
    {
        if (!empty($_POST)) {
            return $_POST;
        }

        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);

        return is_array($data) ? $data : [];
    }

    private function uploadImage(array $file): array
    {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['path' => null];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Có lỗi xảy ra khi upload ảnh.'];
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return ['error' => 'File ảnh không được vượt quá 5MB.'];
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $originalName = $file['name'] ?? '';
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            return ['error' => 'Chỉ cho phép upload file ảnh JPG, JPEG, PNG, GIF hoặc WEBP.'];
        }

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = $finfo ? finfo_file($finfo, $file['tmp_name']) : null;

        if ($finfo) {
            finfo_close($finfo);
        }

        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            return ['error' => 'File tải lên không phải là ảnh hợp lệ.'];
        }

        $targetDir = 'uploads/products/';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $newFileName = 'product_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
        $targetPath = $targetDir . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['error' => 'Không thể lưu file ảnh lên server.'];
        }

        return ['path' => $targetPath];
    }
}
