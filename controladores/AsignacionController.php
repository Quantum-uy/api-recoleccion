<?php

require_once __DIR__ . '/../modelos/AsignacionModel.php';

class AsignacionController
{
    private $modelo;

    public function __construct($conn)
    {
        $this->modelo = new AsignacionModel($conn);
    }

    public function getAll()
    {
        echo json_encode($this->modelo->getAll());
    }

    public function getConductoresDisponibles()
    {
        echo json_encode($this->modelo->getConductoresDisponibles());
    }

    public function asignar($id_ruta, $data)
    {
        $result = $this->modelo->asignar($id_ruta, $data);
        if (isset($result['error'])) http_response_code(400);
        echo json_encode($result);
    }
}
