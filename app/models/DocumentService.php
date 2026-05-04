<?php
class DocumentService
{
    public function secureName(string $filename): string
    {
        $hash = bin2hex(random_bytes(8));
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        return sprintf('%s_%s.%s', date('YmdHis'), $hash, $ext);
    }

    public function validateUpload(array $file): bool
    {
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return $file['error'] === UPLOAD_ERR_OK && in_array($ext, $allowed, true) && $file['size'] <= 5 * 1024 * 1024;
    }
}
