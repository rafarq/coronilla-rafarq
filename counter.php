<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$dataDir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
if (!is_dir($dataDir) && !mkdir($dataDir, 0755, true) && !is_dir($dataDir)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Cannot create data directory']);
    exit;
}

try {
    $db = new PDO('sqlite:' . $dataDir . DIRECTORY_SEPARATOR . 'counter.sqlite', null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $db->exec('CREATE TABLE IF NOT EXISTS stats (name TEXT PRIMARY KEY, value INTEGER NOT NULL DEFAULT 0)');
    $db->exec("INSERT OR IGNORE INTO stats (name, value) VALUES ('completed', 0)");

    $action = $_GET['action'] ?? null;
    if ($action === 'get') {
        $count = (int) $db->query("SELECT value FROM stats WHERE name = 'completed'")->fetchColumn();
        echo json_encode(['success' => true, 'count' => $count]);
        exit;
    }

    $payload = json_decode((string) file_get_contents('php://input'), true);
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_array($payload) || ($payload['action'] ?? null) !== 'increment') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        exit;
    }

    $db->beginTransaction();
    $db->exec("UPDATE stats SET value = value + 1 WHERE name = 'completed'");
    $count = (int) $db->query("SELECT value FROM stats WHERE name = 'completed'")->fetchColumn();
    $db->commit();
    echo json_encode(['success' => true, 'count' => $count]);
} catch (Throwable $error) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Counter unavailable']);
}
