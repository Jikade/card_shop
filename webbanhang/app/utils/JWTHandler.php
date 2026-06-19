<?php
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler
{
    private string $secretKey;
    private string $algorithm = 'HS256';
    private int $ttlSeconds = 86400;

    public function __construct()
    {
        $this->secretKey = getenv('JWT_SECRET') ?: 'HUTECH_CHANGE_ME_2026_Nguyen_Huu_Minh_Hieu_2005';
    }

    public function encode(array $userData): string
    {
        $issuedAt = time();

        $payload = [
            'iat' => $issuedAt,
            'nbf' => $issuedAt,
            'exp' => $issuedAt + $this->ttlSeconds,
            'data' => $userData
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function decode(?string $jwt): ?array
    {
        if (!$jwt) {
            return null;
        }

        try {
            $decoded = JWT::decode($jwt, new Key($this->secretKey, $this->algorithm));
            return json_decode(json_encode($decoded->data), true);
        } catch (Throwable $e) {
            return null;
        }
    }

    public function getTtlSeconds(): int
    {
        return $this->ttlSeconds;
    }
}