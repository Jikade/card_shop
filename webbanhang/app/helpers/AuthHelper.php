<?php
require_once __DIR__ . '/../utils/JWTHandler.php';

class AuthHelper
{
    private const COOKIE_NAME = 'jwt_token';
    private const COOKIE_PATH = '/webbanhang';

    public static function getToken(): ?string
    {
        $authHeader = self::getAuthorizationHeader();

        if ($authHeader && preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
            return $matches[1];
        }

        return $_COOKIE[self::COOKIE_NAME] ?? null;
    }

    public static function getUser(): ?array
    {
        $token = self::getToken();
        return (new JWTHandler())->decode($token);
    }

    public static function isLoggedIn(): bool
    {
        return self::getUser() !== null;
    }

    public static function isAdmin(): bool
    {
        $user = self::getUser();
        return $user && (($user['role'] ?? '') === 'admin');
    }

    public static function getRole(): string
    {
        $user = self::getUser();
        return $user['role'] ?? 'guest';
    }

    public static function requireLogin(bool $jsonResponse = false): array
    {
        $user = self::getUser();

        if ($user) {
            return $user;
        }

        if ($jsonResponse) {
            http_response_code(401);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['message' => 'Bạn cần đăng nhập để thực hiện thao tác này.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        header('Location: /webbanhang/account/login');
        exit;
    }

    public static function requireAdmin(bool $jsonResponse = false): array
    {
        $user = self::requireLogin($jsonResponse);

        if (($user['role'] ?? '') === 'admin') {
            return $user;
        }

        if ($jsonResponse) {
            http_response_code(403);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['message' => 'Bạn không có quyền thực hiện thao tác này.'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        http_response_code(403);
        echo 'Bạn không có quyền truy cập chức năng này!';
        exit;
    }

    public static function setTokenCookie(string $token): void
    {
        $jwtHandler = new JWTHandler();

        setcookie(self::COOKIE_NAME, $token, [
            'expires' => time() + $jwtHandler->getTtlSeconds(),
            'path' => self::COOKIE_PATH,
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        $_COOKIE[self::COOKIE_NAME] = $token;
    }

    public static function clearTokenCookie(): void
    {
        setcookie(self::COOKIE_NAME, '', [
            'expires' => time() - 3600,
            'path' => self::COOKIE_PATH,
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        unset($_COOKIE[self::COOKIE_NAME]);
    }

    private static function getAuthorizationHeader(): ?string
    {
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();

            foreach ($headers as $key => $value) {
                if (strtolower($key) === 'authorization') {
                    return $value;
                }
            }
        }

        return $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? null;
    }
}