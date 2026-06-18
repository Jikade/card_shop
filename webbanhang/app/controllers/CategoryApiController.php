<?php

require_once 'app/config/database.php';
require_once 'app/models/CategoryModel.php';

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
        echo json_encode(
            $this->categoryModel->getCategories(),
            JSON_UNESCAPED_UNICODE
        );
    }
}
