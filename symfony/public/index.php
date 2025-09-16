<?php

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/app.php';

$templateRenderer = new \App\Framework\TemplateRenderer(__DIR__ . '/../templates');

$eventBasePath = $config['event_base_path'] ?? (__DIR__ . '/../var/events');
if (!str_starts_with($eventBasePath, DIRECTORY_SEPARATOR) && !preg_match('#^[A-Za-z]:[\\/]#', $eventBasePath)) {
    $combined = realpath(__DIR__ . '/../' . ltrim($eventBasePath, '/\\'));
    if ($combined !== false) {
        $eventBasePath = $combined;
    }
}

$eventFileService = new \App\Service\EventFileService($eventBasePath);
$databaseConfig = $config['database'] ?? [];
$archivoLisRepository = \App\Repository\ArchivoLisRepository::fromConfig($databaseConfig);

$controller = new \App\Controller\EventController($templateRenderer, $eventFileService, $archivoLisRepository);

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$queryParams = $_GET;
$postData = $_POST;

if ($uri === '/' || $uri === '') {
    $response = $controller->index();
} elseif (preg_match('#^/event/([^/]+)/download$#', $uri, $matches)) {
    $eventCode = urldecode($matches[1]);
    $response = $controller->download($eventCode, $postData, $queryParams, $method);
} elseif (preg_match('#^/event/([^/]+)$#', $uri, $matches)) {
    $eventCode = urldecode($matches[1]);
    $response = $controller->show($eventCode, $queryParams);
} else {
    $response = new \App\Framework\Response('Ruta no encontrada', 404);
}

if ($response instanceof \App\Framework\FileResponse) {
    $filePath = $response->getFilePath();
    if (!is_file($filePath)) {
        http_response_code(404);
        echo 'Archivo no encontrado.';
        exit;
    }

    header('Content-Description: File Transfer');
    header('Content-Type: ' . $response->getMimeType());
    header('Content-Disposition: attachment; filename="' . basename($response->getDownloadName()) . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: public');

    $handle = fopen($filePath, 'rb');
    if ($handle !== false) {
        while (!feof($handle)) {
            echo fread($handle, 8192);
        }
        fclose($handle);
    }

    if ($response->shouldDeleteAfterSend()) {
        @unlink($filePath);
    }

    exit;
}

if (!$response instanceof \App\Framework\Response) {
    $response = new \App\Framework\Response((string) $response);
}

http_response_code($response->getStatus());
foreach ($response->getHeaders() as $header => $value) {
    header($header . ': ' . $value);
}

echo $response->getContent();
