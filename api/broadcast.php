<?php
/**
 * Admin-only proxy for POST /broadcast -> push.schoettner.dev.
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
require_once dirname(__FILE__) . "/config.php";

$payload = json_decode(file_get_contents("php://input"), true);
if (!is_array($payload)) {
    respond(400, ["error" => "invalid_request"]);
}

$adminKey = isset($payload["admin"]) && is_string($payload["admin"]) ? $payload["admin"] : "";
if ($adminKey === "" || $adminKey !== PSEUDO_ADMIM_PASSWORD) {
    respond(403, ["error" => "forbidden"]);
}

$eventId = isset($payload["event_id"]) && is_string($payload["event_id"]) ? $payload["event_id"] : "";
$eventTitle = isset($payload["title"]) && is_string($payload["title"]) ? trim($payload["title"]) : "";
if (!preg_match('/^[A-Za-z0-9_-]+$/', $eventId) || $eventTitle === "" || strlen($eventTitle) > 500) {
    respond(400, ["error" => "invalid_event"]);
}

$broadcastToken = BROADCAST_TOKEN;
if ($broadcastToken === "" || $broadcastToken === "xxx") {
    respond(500, ["error" => "broadcast_not_configured"]);
}

$broadcast = [
    "title" => "Jetzt anmelden!",
    "body" => $eventTitle,
    "url" => SITE_ADDRESS . "?view=book&event_id=" . rawurlencode($eventId),
];

$ch = curl_init("https://push.schoettner.dev/broadcast");
if ($ch === false) {
    respond(502, ["error" => "upstream_unreachable"]);
}

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($broadcast));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer " . $broadcastToken,
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $httpCode < 200 || $httpCode >= 300) {
    respond(502, ["error" => "broadcast_failed"]);
}

$result = json_decode($response, true);
if (!is_array($result) || !isset($result["sent"], $result["failed"])) {
    respond(502, ["error" => "invalid_upstream_response"]);
}

respond(200, $result);
