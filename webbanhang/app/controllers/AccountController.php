<?php
require_once 'app/config/database.php';
require_once 'app/models/AccountModel.php';
require_once 'app/utils/JWTHandler.php';
require_once 'app/helpers/AuthHelper.php';

class AccountController
{
    private $accountModel;
    private $db;
    private $jwtHandler;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }

    public function register()
    {
        include_once 'app/views/account/register.php';
    }

    public function login()
    {
        include_once 'app/views/account/login.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /webbanhang/account/register');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $fullName = trim($_POST['fullname'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmpassword'] ?? '';
        $errors = [];

        if ($username === '') {
            $errors['username'] = 'Vui lòng nhập username!';
        }

        if ($fullName === '') {
            $errors['fullname'] = 'Vui lòng nhập họ tên!';
        }

        if ($password === '') {
            $errors['password'] = 'Vui lòng nhập password!';
        }

        if ($password !== $confirmPassword) {
            $errors['confirmPass'] = 'Mật khẩu và xác nhận mật khẩu chưa đúng.';
        }

        if ($this->accountModel->getAccountByUsername($username)) {
            $errors['account'] = 'Tài khoản này đã có người đăng ký!';
        }

        if (!empty($errors)) {
            include_once 'app/views/account/register.php';
            return;
        }

        // Không hash ở Controller. AccountModel::save() sẽ hash đúng 1 lần.
        $result = $this->accountModel->save($username, $fullName, $password, 'user');

        if ($result) {
            header('Location: /webbanhang/account/login');
            exit;
        }

        $errors['account'] = 'Đăng ký thất bại. Vui lòng thử lại.';
        include_once 'app/views/account/register.php';
    }

    public function checkLogin()
    {
        $isJsonRequest = $this->isJsonRequest();

        if ($isJsonRequest) {
            $data = json_decode(file_get_contents('php://input'), true) ?: [];
            $username = trim($data['username'] ?? '');
            $password = $data['password'] ?? '';
        } else {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
        }

        $user = $this->accountModel->getAccountByUsername($username);

        if (!$user || !password_verify($password, $user->password)) {
            if ($isJsonRequest) {
                http_response_code(401);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['message' => 'Sai tên đăng nhập hoặc mật khẩu.'], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $error = 'Sai tên đăng nhập hoặc mật khẩu.';
            include_once 'app/views/account/login.php';
            return;
        }

        $payload = [
            'id' => (int) $user->id,
            'username' => $user->username,
            'fullname' => $user->fullname,
            'role' => $user->role
        ];

        $token = $this->jwtHandler->encode($payload);
        AuthHelper::setTokenCookie($token);

        if ($isJsonRequest) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'message' => 'Đăng nhập thành công.',
                'token' => $token,
                'user' => $payload
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        header('Location: /webbanhang/Product');
        exit;
    }

    public function me()
    {
        header('Content-Type: application/json; charset=UTF-8');

        $user = AuthHelper::getUser();

        if (!$user) {
            http_response_code(401);
            echo json_encode(['message' => 'Chưa đăng nhập.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode(['user' => $user], JSON_UNESCAPED_UNICODE);
    }

    public function logout()
    {
        AuthHelper::clearTokenCookie();

        // Chỉ xóa dữ liệu giỏ hàng cũ, không dùng session để xác thực nữa.
        unset($_SESSION['cart']);

        header('Location: /webbanhang/Product');
        exit;
    }

    private function isJsonRequest(): bool
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

        return stripos($contentType, 'application/json') !== false
            || stripos($accept, 'application/json') !== false
            || strtolower($requestedWith) === 'xmlhttprequest';
    }
}
