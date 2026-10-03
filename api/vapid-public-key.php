<?php
/**
 * Proxy for GET /vapid-public-key -> push.schoettner.dev
 * Runs same-origin on ingolstadt-chapter.de to avoid cross-origin fetch issues
 * in iOS Safari (standalone PWA).
 */

header("Content-Type: application/json");

$ch = curl_init("https://push.schoettner.dev/vapid-public-key");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($response === false) {
    http_response_code(502);
    echo json_encode(["error" => "upstream_unreachable", "detail" => $error]);
    exit;
}

http_response_code($httpCode);
echo $response;
