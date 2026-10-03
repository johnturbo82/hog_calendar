<?php
/**
 * Admin-only proxy for GET /subscriptions/count -> push.schoettner.dev.
 */

header("Content-Type: application/json; charset=utf-8");

function respond($statusCode, $payload)
{
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    respond(405, ["error" => "method_not_allowed"]);
}

require_once dirname(__DIR__) . "/config.php";

$payload = json_decode(file_get_contents("php://input"), true);
$adminKey = is_array($payload) && isset($payload["admin"]) && is_string($payload["admin"])
    ? $payload["admin"]
    : "";
if ($adminKey === "" || $adminKey !== PSEUDO_ADMIM_PASSWORD) {
    respond(403, ["error" => "forbidden"]);
}

$broadcastToken = defined("BROADCAST_TOKEN") ? BROADCAST_TOKEN : "";
if ($broadcastToken === "" || $broadcastToken === "xxx") {
    respond(500, ["error" => "broadcast_not_configured"]);
}

$ch = curl_init("https://push.schoettner.dev/subscriptions/count");
if ($ch === false) {
    respond(502, ["error" => "upstream_unreachable"]);
}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $broadcastToken]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $httpCode < 200 || $httpCode >= 300) {
    respond(502, ["error" => "count_unavailable"]);
}

$result = json_decode($response, true);
if (!is_array($result) || !isset($result["count"]) || !is_int($result["count"])) {
    respond(502, ["error" => "invalid_upstream_response"]);
}

respond(200, $result);
