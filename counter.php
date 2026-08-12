<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$file = __DIR__ . DIRECTORY_SEPARATOR . 'count.txt';
if (!file_exists($file) && file_put_contents($file, '0', LOCK_EX) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Counter unavailable']);
    exit;
}

$action = $_GET['action'] ?? null;

if ($action === 'get' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $count = (int) (file_get_contents($file) ?: '0');
    echo json_encode(['success' => true, 'count' => $count]);
    exit;
}

$payload = json_decode((string) file_get_contents('php://input'), true);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_array($payload) || ($payload['action'] ?? null) !== 'increment') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

$handle = fopen($file, 'c+');
if ($handle === false || !flock($handle, LOCK_EX)) {
    if (is_resource($handle)) fclose($handle);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Counter unavailable']);
    exit;
}

rewind($handle);
$count = (int) (stream_get_contents($handle) ?: '0');
$count++;
ftruncate($handle, 0);
rewind($handle);
fwrite($handle, (string) $count);
fflush($handle);
flock($handle, LOCK_UN);
fclose($handle);

echo json_encode(['success' => true, 'count' => $count]);
