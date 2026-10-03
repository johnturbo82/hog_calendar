<?php
/**
 * Proxy for POST /subscribe -> push.schoettner.dev
 * Runs same-origin on ingolstadt-chapter.de.
 */

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "method_not_allowed"]);
    exit;
}

$body = file_get_contents("php://input");

$ch = curl_init("https://push.schoettner.dev/subscribe");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
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
