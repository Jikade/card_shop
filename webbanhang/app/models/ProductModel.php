<?php

class ProductModel
{
    private $conn;
    private $table_name = 'product';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    private function validate($name, $description, $price, $category_id)
    {
        $errors = [];

        if (trim((string) $name) === '') {
            $errors['name'] = 'Tên sản phẩm không được để trống';
        }

        if (trim((string) $description) === '') {
            $errors['description'] = 'Mô tả không được để trống';
        }

        if (!is_numeric($price) || $price < 0) {
            $errors['price'] = 'Giá sản phẩm không hợp lệ';
        }

        if (empty($category_id) || !is_numeric($category_id)) {
            $errors['category_id'] = 'Danh mục không hợp lệ';
        }

        return $errors;
    }

    public function getProducts()
    {
        $query = "
            SELECT
                p.id,
                p.name,
                p.description,
                p.price,
                p.image,
                p.category_id,
                c.name AS category_name
            FROM {$this->table_name} p
            LEFT JOIN category c ON p.category_id = c.id
            ORDER BY p.id DESC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getProductById($id)
    {
        $query = "
            SELECT
                p.id,
                p.name,
                p.description,
                p.price,
                p.image,
                p.category_id,
                c.name AS category_name
            FROM {$this->table_name} p
            LEFT JOIN category c ON p.category_id = c.id
            WHERE p.id = :id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function addProduct($name, $description, $price, $category_id, $image = null)
    {
        $errors = $this->validate($name, $description, $price, $category_id);

        if (!empty($errors)) {
            return $errors;
        }

        $query = "
            INSERT INTO {$this->table_name}
                (name, description, price, category_id, image)
            VALUES
                (:name, :description, :price, :category_id, :image)
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':name', trim(strip_tags($name)));
        $stmt->bindValue(':description', trim(strip_tags($description)));
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':category_id', (int) $category_id, PDO::PARAM_INT);
        $stmt->bindValue(':image', $image ?: null);

        return $stmt->execute();
    }

    public function updateProduct($id, $name, $description, $price, $category_id, $image = null)
    {
        $errors = $this->validate($name, $description, $price, $category_id);

        if (!empty($errors)) {
            return $errors;
        }

        $query = "
            UPDATE {$this->table_name}
            SET
                name = :name,
                description = :description,
                price = :price,
                category_id = :category_id
        ";

        if ($image !== null) {
            $query .= ', image = :image';
        }

        $query .= ' WHERE id = :id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->bindValue(':name', trim(strip_tags($name)));
        $stmt->bindValue(':description', trim(strip_tags($description)));
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':category_id', (int) $category_id, PDO::PARAM_INT);

        if ($image !== null) {
            $stmt->bindValue(':image', $image ?: null);
        }

        return $stmt->execute();
    }

    public function deleteProduct($id)
    {
        try {
            $query = "DELETE FROM {$this->table_name} WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return [
                    'success' => false,
                    'message' => 'Không thể xóa sản phẩm đã xuất hiện trong hóa đơn.'
                ];
            }

            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa sản phẩm.'
            ];
        }
    }
}
