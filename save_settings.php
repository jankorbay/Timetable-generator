<?php
// Saves the signed-in user's template style, or drops a shared style they declined.
// Accepts only a JSON body, which a cross-site HTML form cannot send.
session_start();
header('Content-Type: application/json');

$body = json_decode(file_get_contents('php://input'), true);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !str_starts_with($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') || !is_array($body)) {
    http_response_code(400);
    exit('{"ok":false}');
}

if (($body['action'] ?? '') === 'discard') {
    unset($_SESSION['shared_settings']);
    exit('{"ok":true}');
}

if (!isset($_SESSION['google_user']['email'])) {
    http_response_code(401);
    exit('{"ok":false}');
}

require_once __DIR__ . '/templates/settings.php';
$templates = require __DIR__ . '/templates/list.php';
if (!saveSettings($_SESSION['google_user']['email'], cleanSettings($body['settings'] ?? null, $templates))) {
    error_log('Saving template settings failed: ' . (error_get_last()['message'] ?? 'unknown'));
    http_response_code(500);
    exit('{"ok":false}');
}
unset($_SESSION['shared_settings']);
echo '{"ok":true}';
