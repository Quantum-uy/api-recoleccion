<?php

require_once __DIR__ . '/../modelos/FlotaModel.php';

class FlotaController
{
    private $modelo;

    public function __construct($conn)
    {
        $this->modelo = new FlotaModel($conn);
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
            echo json_encode(["error" => "Camión no encontrado"]);
        }
    }

    public function getDisponibles()
    {
        echo json_encode($this->modelo->getDisponibles());
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
