<?php
require_once 'app/config/database.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/helpers/AuthHelper.php';

class CategoryApiController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    public function index()
    {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($this->categoryModel->getCategories(), JSON_UNESCAPED_UNICODE);
    }

    public function show($id)
    {
        header('Content-Type: application/json; charset=UTF-8');

        $category = $this->categoryModel->getCategoryById($id);

        if ($category) {
            echo json_encode($category, JSON_UNESCAPED_UNICODE);
            return;
        }

        http_response_code(404);
        echo json_encode(['message' => 'Không tìm thấy danh mục.'], JSON_UNESCAPED_UNICODE);
    }

    public function store()
    {
        AuthHelper::requireAdmin(true);
        header('Content-Type: application/json; charset=UTF-8');

        $data = json_decode(file_get_contents('php://input'), true) ?: [];
        $result = $this->categoryModel->addCategory($data['name'] ?? '', $data['description'] ?? '');

        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result], JSON_UNESCAPED_UNICODE);
            return;
        }

        http_response_code(201);
        echo json_encode(['message' => 'Thêm danh mục thành công.'], JSON_UNESCAPED_UNICODE);
    }

    public function update($id)
    {
        AuthHelper::requireAdmin(true);
        header('Content-Type: application/json; charset=UTF-8');

        $data = json_decode(file_get_contents('php://input'), true) ?: [];
        $result = $this->categoryModel->updateCategory($id, $data['name'] ?? '', $data['description'] ?? '');

        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode(['message' => 'Cập nhật danh mục thành công.'], JSON_UNESCAPED_UNICODE);
    }

    public function destroy($id)
    {
        AuthHelper::requireAdmin(true);
        header('Content-Type: application/json; charset=UTF-8');

        $result = $this->categoryModel->deleteCategory($id);

        if ($result === true) {
            echo json_encode(['message' => 'Xóa danh mục thành công.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        http_response_code(400);
        echo json_encode([
            'errors' => is_array($result) ? $result : ['delete' => 'Xóa danh mục thất bại.']
        ], JSON_UNESCAPED_UNICODE);
    }
}
