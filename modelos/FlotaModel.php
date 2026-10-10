<?php

class FlotaModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $sql = "SELECT c.*, tr.nombre AS tipo_residuo,
                       ca.nombre AS centro_nombre,
                       u.nombre AS conductor_nombre, u.apellido AS conductor_apellido
                FROM camion c
                LEFT JOIN tipo_residuo tr ON c.id_tipo_residuo = tr.id_tipo_residuo
                LEFT JOIN centro_acopio ca ON c.id_centro = ca.id_centro
                LEFT JOIN conductor co ON co.id_camion = c.id_camion
                LEFT JOIN usuario u ON u.id_usuario = co.id_usuario
                ORDER BY c.id_camion DESC";
        $result = mysqli_query($this->conn, $sql);
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
        return $items;
    }

    public function getById($id)
    {
        $stmt = mysqli_prepare($this->conn,
            "SELECT c.*, tr.nombre AS tipo_residuo
             FROM camion c
             LEFT JOIN tipo_residuo tr ON c.id_tipo_residuo = tr.id_tipo_residuo
             WHERE c.id_camion = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    public function updateEstado($id, $estado)
    {
        $stmt = mysqli_prepare($this->conn, "UPDATE camion SET estado = ? WHERE id_camion = ?");
        mysqli_stmt_bind_param($stmt, "si", $estado, $id);
        if (mysqli_stmt_execute($stmt)) {
            return ["success" => "Estado actualizado"];
        }
        return ["error" => "No se pudo actualizar"];
    }

    public function getDisponibles()
    {
        $result = mysqli_query($this->conn,
            "SELECT id_camion, matricula, modelo FROM camion WHERE estado = 'disponible' ORDER BY matricula");
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
        return $items;
    }
}
