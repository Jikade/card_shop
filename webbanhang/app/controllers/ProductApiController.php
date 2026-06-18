<?php

require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/helpers/SessionHelper.php';

class ProductApiController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    private function respond($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private function requireAdmin()
    {
        if (!SessionHelper::isAdmin()) {
            $this->respond(
                ['message' => 'Bạn không có quyền thực hiện thao tác này'],
                403
            );
            exit;
        }
    }

    private function readJsonBody()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data)) {
            $this->respond(['message' => 'Dữ liệu JSON không hợp lệ'], 400);
            exit;
        }

        return $data;
    }

    public function index()
    {
        $this->respond($this->productModel->getProducts());
    }

    public function show($id)
    {
        $product = $this->productModel->getProductById($id);

        if (!$product) {
            $this->respond(['message' => 'Không tìm thấy sản phẩm'], 404);
            return;
        }

        $this->respond($product);
    }

    public function store()
    {
        $this->requireAdmin();
        $data = $this->readJsonBody();

        $result = $this->productModel->addProduct(
            $data['name'] ?? '',
            $data['description'] ?? '',
            $data['price'] ?? '',
            $data['category_id'] ?? null
        );

        if (is_array($result)) {
            $this->respond(['errors' => $result], 400);
            return;
        }

        if (!$result) {
            $this->respond(['message' => 'Thêm sản phẩm thất bại'], 500);
            return;
        }

        $this->respond(['message' => 'Thêm sản phẩm thành công'], 201);
    }

    public function update($id)
    {
        $this->requireAdmin();

        if (!$this->productModel->getProductById($id)) {
            $this->respond(['message' => 'Không tìm thấy sản phẩm'], 404);
            return;
        }

        $data = $this->readJsonBody();

        $result = $this->productModel->updateProduct(
            $id,
            $data['name'] ?? '',
            $data['description'] ?? '',
            $data['price'] ?? '',
            $data['category_id'] ?? null
        );

        if (is_array($result)) {
            $this->respond(['errors' => $result], 400);
            return;
        }

        if (!$result) {
            $this->respond(['message' => 'Cập nhật sản phẩm thất bại'], 500);
            return;
        }

        $this->respond(['message' => 'Cập nhật sản phẩm thành công']);
    }

    public function destroy($id)
    {
        $this->requireAdmin();

        if (!$this->productModel->getProductById($id)) {
            $this->respond(['message' => 'Không tìm thấy sản phẩm'], 404);
            return;
        }

        $result = $this->productModel->deleteProduct($id);

        if (is_array($result)) {
            $status = $result['success'] ? 200 : 409;
            $this->respond(['message' => $result['message']], $status);
            return;
        }

        if (!$result) {
            $this->respond(['message' => 'Xóa sản phẩm thất bại'], 500);
            return;
        }

        $this->respond(['message' => 'Xóa sản phẩm thành công']);
    }
}
