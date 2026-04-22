<?php

declare(strict_types=1);

require_once __DIR__ . '/Task.php';
require_once __DIR__ . '/TaskRepository.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$repo = new TaskRepository();

$method = $_SERVER['REQUEST_METHOD'];

$input = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    switch ($method) {
        case 'GET':
            $status = $_GET['status'] ?? null;
            $tasks = $repo->listar($status);
            echo json_encode(array_map(fn(Task $t) => $t->toArray(), $tasks));
            break;

        case 'POST':
            if (!isset($input['titulo']) || trim($input['titulo']) === '') {
                http_response_code(400);
                echo json_encode(['error' => 'Título é obrigatório']);
                break;
            }

            if (strlen($input['titulo']) > 100) {
                http_response_code(400);
                echo json_encode(['error' => 'Título deve ter no máximo 100 caracteres']);
                break;
            }

            $task = new Task(trim($input['titulo']));
            $task = $repo->criar($task);

            http_response_code(201);
            echo json_encode($task->toArray());
            break;

        case 'PATCH':
            $id = $input['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID da tarefa é obrigatório']);
                break;
            }

            $task = $repo->buscar($id);

            if (!$task) {
                http_response_code(404);
                echo json_encode(['error' => 'Tarefa não encontrada']);
                break;
            }

            if (isset($input['status'])) {
                if (!in_array($input['status'], ['pendente', 'concluida'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Status inválido']);
                    break;
                }
                $task->status = $input['status'];
            }

            $repo->atualizar($task);
            echo json_encode($task->toArray());
            break;

        case 'DELETE':
            $id = $input['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID da tarefa é obrigatório']);
                break;
            }

            if (!$repo->deletar($id)) {
                http_response_code(404);
                echo json_encode(['error' => 'Tarefa não encontrada']);
            } else {
                http_response_code(200);
            }

            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor']);
}
