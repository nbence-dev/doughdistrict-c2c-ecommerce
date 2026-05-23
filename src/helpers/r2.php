<?php
/**
 * Uploads a $_FILES entry to Cloudflare R2 using the AWS SDK for PHP (S3-compatible).
 * Returns the public CDN URL on success.
 * Throws RuntimeException on validation failure or upload error.
 */

require_once dirname(ROOT_PATH) . '/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

function upload_to_r2(array $file): string
{
    $accountId = getenv('R2_ACCOUNT_ID');
    $accessKey = getenv('R2_ACCESS_KEY_ID');
    $secretKey = getenv('R2_SECRET_ACCESS_KEY');
    $bucket    = getenv('R2_BUCKET');
    $cdnUrl    = rtrim(getenv('R2_CDN_URL'), '/');

    if (!$accountId || !$accessKey || !$secretKey || !$bucket || !$cdnUrl) {
        throw new RuntimeException('R2 configuration is incomplete. Check your .env file.');
    }

    // ── Validate the upload ───────────────────────────────────────────────────

    $uploadErrors = [
        UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the server upload limit.',
        UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the form size limit.',
        UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
    ];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException($uploadErrors[$file['error']] ?? 'File upload failed.');
    }

    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $mimeType     = mime_content_type($file['tmp_name']);

    if (!in_array($mimeType, $allowedMimes, true)) {
        throw new RuntimeException('Only JPG, PNG, WebP, and GIF images are allowed.');
    }

    // ── Build object key ──────────────────────────────────────────────────────

    $mimeToExt = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $ext = $mimeToExt[$mimeType];
    $key = 'products/' . bin2hex(random_bytes(16)) . '.' . $ext;

    // ── Upload via AWS SDK ────────────────────────────────────────────────────

    $client = new S3Client([
        'version'                 => 'latest',
        'region'                  => 'auto',
        'endpoint'                => "https://{$accountId}.r2.cloudflarestorage.com",
        'credentials'             => [
            'key'    => $accessKey,
            'secret' => $secretKey,
        ],
        'use_path_style_endpoint' => true,
    ]);

    try {
        $client->putObject([
            'Bucket'      => $bucket,
            'Key'         => $key,
            'SourceFile'  => $file['tmp_name'],
            'ContentType' => $mimeType,
        ]);
    } catch (AwsException $e) {
        throw new RuntimeException('R2 upload failed: ' . $e->getAwsErrorMessage());
    }

    return "{$cdnUrl}/{$key}";
}

function delete_from_r2(string $imageUrl): void
{
    $accountId = getenv('R2_ACCOUNT_ID');
    $accessKey = getenv('R2_ACCESS_KEY_ID');
    $secretKey = getenv('R2_SECRET_ACCESS_KEY');
    $bucket    = getenv('R2_BUCKET');
    $cdnUrl    = rtrim(getenv('R2_CDN_URL'), '/');

    if (!$accountId || !$accessKey || !$secretKey || !$bucket || !$cdnUrl) {
        return;
    }

    // Derive the object key by stripping the CDN base URL
    $key = ltrim(substr($imageUrl, strlen($cdnUrl)), '/');
    if (!$key) {
        return;
    }

    $client = new S3Client([
        'version'                 => 'latest',
        'region'                  => 'auto',
        'endpoint'                => "https://{$accountId}.r2.cloudflarestorage.com",
        'credentials'             => [
            'key'    => $accessKey,
            'secret' => $secretKey,
        ],
        'use_path_style_endpoint' => true,
    ]);

    try {
        $client->deleteObject(['Bucket' => $bucket, 'Key' => $key]);
    } catch (AwsException $e) {
        // Non-fatal — log and continue
        error_log('R2 delete failed for key ' . $key . ': ' . $e->getAwsErrorMessage());
    }
}
