<?php

session_start();

require_once 'app/helpers/SessionHelper.php';

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = $url === '' ? [] : explode('/', $url);

$controllerName = isset($urlParts[0]) && $urlParts[0] !== ''
    ? ucfirst($urlParts[0]) . 'Controller'
    : 'DefaultController';

$action = isset($urlParts[1]) && $urlParts[1] !== ''
    ? $urlParts[1]
    : 'index';

// REST API: /api/product, /api/product/{id}, /api/category
if ($controllerName === 'ApiController' && isset($urlParts[1])) {
    header('Content-Type: application/json; charset=UTF-8');

    $apiControllerName = ucfirst($urlParts[1]) . 'ApiController';
    $apiControllerPath = 'app/controllers/' . $apiControllerName . '.php';

    if (!file_exists($apiControllerPath)) {
        http_response_code(404);
        echo json_encode(['message' => 'Controller not found'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    require_once $apiControllerPath;

    $controller = new $apiControllerName();
    $method = $_SERVER['REQUEST_METHOD'];

    // HTML form + FormData không upload file tốt với PUT trực tiếp trong PHP.
    // Vì vậy cho phép gửi POST kèm _method=PUT hoặc header X-HTTP-Method-Override.
    $methodOverride = $_POST['_method']
        ?? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']
        ?? null;

    if ($method === 'POST' && $methodOverride) {
        $methodOverride = strtoupper($methodOverride);

        if (in_array($methodOverride, ['PUT', 'DELETE'], true)) {
            $method = $methodOverride;
        }
    }

    $id = $urlParts[2] ?? null;

    switch ($method) {
        case 'GET':
            $action = $id !== null ? 'show' : 'index';
            break;

        case 'POST':
            $action = 'store';
            break;

        case 'PUT':
            if ($id === null) {
                http_response_code(400);
                echo json_encode(['message' => 'ID is required'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $action = 'update';
            break;

        case 'DELETE':
            if ($id === null) {
                http_response_code(400);
                echo json_encode(['message' => 'ID is required'], JSON_UNESCAPED_UNICODE);
                exit;
            }
            $action = 'destroy';
            break;

        default:
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed'], JSON_UNESCAPED_UNICODE);
            exit;
    }

    if (!method_exists($controller, $action)) {
        http_response_code(404);
        echo json_encode(['message' => 'Action not found'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($id !== null) {
        call_user_func_array([$controller, $action], [$id]);
    } else {
        call_user_func_array([$controller, $action], []);
    }

    exit;
}

// MVC web thông thường
$controllerPath = 'app/controllers/' . $controllerName . '.php';

if (!file_exists($controllerPath)) {
    die('Controller not found');
}

require_once $controllerPath;
$controller = new $controllerName();

if (!method_exists($controller, $action)) {
    die('Action not found');
}

call_user_func_array([$controller, $action], array_slice($urlParts, 2));
