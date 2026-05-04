<?php
class EncryptionService
{
    private string $method = 'AES-256-CBC';
    private string $key;

    public function __construct()
    {
        $this->key = hash('sha256', 'mindbridge_secure_secret', true);
    }

    public function encrypt(string $data): string
    {
        $iv = random_bytes(openssl_cipher_iv_length($this->method));
        $encrypted = openssl_encrypt($data, $this->method, $this->key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public function decrypt(string $payload): string
    {
        $raw = base64_decode($payload);
        $ivLength = openssl_cipher_iv_length($this->method);
        $iv = substr($raw, 0, $ivLength);
        $encrypted = substr($raw, $ivLength);
        return openssl_decrypt($encrypted, $this->method, $this->key, 0, $iv) ?: '';
    }
}
