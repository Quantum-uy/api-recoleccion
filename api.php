<?php

require_once 'config.php';
require_once __DIR__ . '/../auth.php';
require_once 'controladores/FlotaController.php';
require_once 'controladores/MantenimientoController.php';
require_once 'controladores/AsignacionController.php';

$flotaCtrl       = new FlotaController($conn);
$mantCtrl        = new MantenimientoController($conn);
$asignacionCtrl  = new AsignacionController($conn);

$method   = $_SERVER['REQUEST_METHOD'];
$uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/sigeru/api-recoleccion';
$endpoint = str_replace($basePath, '', $uri);

switch ($method) {
    case 'GET':
        if ($endpoint === '/flota') {
            $flotaCtrl->getAll();
        } elseif ($endpoint === '/flota/disponibles') {
            $flotaCtrl->getDisponibles();
        } elseif (preg_match('/^\/flota\/(\d+)$/', $endpoint, $m)) {
            $flotaCtrl->getById($m[1]);
        } elseif ($endpoint === '/mantenimiento') {
            $mantCtrl->getAll();
        } elseif (preg_match('/^\/mantenimiento\/(\d+)$/', $endpoint, $m)) {
            $mantCtrl->getById($m[1]);
        } elseif ($endpoint === '/asignaciones') {
            $asignacionCtrl->getAll();
        } elseif ($endpoint === '/conductores-disponibles') {
            $asignacionCtrl->getConductoresDisponibles();
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if ($endpoint === '/mantenimiento') {
            requiereRol(['administrador', 'operario']);
            $mantCtrl->create($data);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (preg_match('/^\/asignaciones\/(\d+)$/', $endpoint, $m)) {
            requiereRol(['administrador', 'operario']);
            $asignacionCtrl->asignar($m[1], $data);
        } elseif (preg_match('/^\/mantenimiento\/(\d+)\/estado$/', $endpoint, $m)) {
            requiereRol(['administrador', 'operario']);
            $mantCtrl->updateEstado($m[1], $data);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    case 'PATCH':
        $data = json_decode(file_get_contents('php://input'), true);
        if (preg_match('/^\/flota\/(\d+)\/estado$/', $endpoint, $m)) {
            requiereRol(['administrador', 'conductor']);
            $flotaCtrl->updateEstado($m[1], $data);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Endpoint no encontrado"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
}
