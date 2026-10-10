<?php

require_once __DIR__ . '/../modelos/MantenimientoModel.php';

class MantenimientoController
{
    private $modelo;

    public function __construct($conn)
    {
        $this->modelo = new MantenimientoModel($conn);
    }

    public function getAll()
    {
        echo json_encode($this->modelo->getAll());
    }

    public function getById($id)
    {
        $item = $this->modelo->getById($id);
        if ($item) {
            echo json_encode($item);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Registro no encontrado"]);
        }
    }

    public function create($data)
    {
        if (empty($data['id_camion']) || empty($data['tipo'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos: id_camion, tipo"]);
            return;
        }
        $result = $this->modelo->create($data);
        if (isset($result['error'])) {
            http_response_code(400);
        } else {
            http_response_code(201);
        }
        echo json_encode($result);
    }

    public function updateEstado($id, $data)
    {
        if (empty($data['estado'])) {
            http_response_code(400);
            echo json_encode(["error" => "Falta el campo: estado"]);
            return;
        }
        $result = $this->modelo->updateEstado($id, $data['estado']);
        if (isset($result['error'])) http_response_code(400);
        echo json_encode($result);
    }
}
