<?php

class CategoryModel
{
    private $conn;
    private $table_name = "category";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getCategories()
    {
        $query = "SELECT id, name, description
                  FROM " . $this->table_name . "
                  ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getCategoryById($id)
    {
        $query = "SELECT id, name, description
                  FROM " . $this->table_name . "
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function addCategory($name, $description)
    {
        $errors = [];

        if (empty(trim($name))) {
            $errors['name'] = 'Tên danh mục không được để trống';
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $query = "INSERT INTO " . $this->table_name . "
                    (name, description)
                  VALUES
                    (:name, :description)";

        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);

        return $stmt->execute();
    }

    public function updateCategory($id, $name, $description)
    {
        $errors = [];

        if (empty(trim($name))) {
            $errors['name'] = 'Tên danh mục không được để trống';
        }

        if (count($errors) > 0) {
            return $errors;
        }

        $query = "UPDATE " . $this->table_name . "
                  SET
                    name = :name,
                    description = :description
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);

        return $stmt->execute();
    }

    public function deleteCategory($id)
    {
        $checkQuery = "SELECT COUNT(*) AS total
                       FROM product
                       WHERE category_id = :id";

        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id);
        $checkStmt->execute();

        $row = $checkStmt->fetch(PDO::FETCH_OBJ);

        if ($row && $row->total > 0) {
            return [
                'delete' => 'Không thể xóa danh mục vì đang có sản phẩm sử dụng danh mục này.'
            ];
        }

        $query = "DELETE FROM " . $this->table_name . "
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}