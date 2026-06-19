<?php

class AccountModel
{
    private $conn;
    private $table_name = 'account';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAccountByUsername($username)
    {
        $query = "SELECT id, username, fullname, password, role
                  FROM {$this->table_name}
                  WHERE username = :username
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function save($username, $fullName, $password, $role = 'user')
    {
        if ($this->getAccountByUsername($username)) {
            return false;
        }

        $query = "INSERT INTO {$this->table_name}
                    (username, fullname, password, role)
                  VALUES
                    (:username, :fullname, :password, :role)";

        $stmt = $this->conn->prepare($query);

        $username = htmlspecialchars(strip_tags($username));
        $fullName = htmlspecialchars(strip_tags($fullName));
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $role = in_array($role, ['admin', 'user'], true) ? $role : 'user';

        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':fullname', $fullName);
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->bindValue(':role', $role);

        return $stmt->execute();
    }
}