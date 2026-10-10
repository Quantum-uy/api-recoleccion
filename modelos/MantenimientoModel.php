<?php

class MantenimientoModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $sql = "SELECT m.*, c.matricula, c.modelo
                FROM mantenimiento_camion m
                JOIN camion c ON m.id_camion = c.id_camion
                ORDER BY m.fecha DESC";
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
            "SELECT m.*, c.matricula FROM mantenimiento_camion m
             JOIN camion c ON m.id_camion = c.id_camion
             WHERE m.id_mantenimiento = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    public function create($data)
    {
        $stmt = mysqli_prepare($this->conn,
            "INSERT INTO mantenimiento_camion (id_camion, fecha, tipo, descripcion, estado)
             VALUES (?, ?, ?, ?, ?)");
        $estado = $data['estado'] ?? 'pendiente';
        $fecha  = $data['fecha'] ?? date('Y-m-d');
        mysqli_stmt_bind_param($stmt, "issss",
            $data['id_camion'], $fecha, $data['tipo'], $data['descripcion'], $estado);
        if (mysqli_stmt_execute($stmt)) {
            // poner camion en mantenimiento
            mysqli_query($this->conn,
                "UPDATE camion SET estado='mantenimiento' WHERE id_camion=" . (int)$data['id_camion']);
            return ["success" => "Mantenimiento registrado", "id" => mysqli_insert_id($this->conn)];
        }
        return ["error" => "No se pudo registrar"];
    }

    public function updateEstado($id, $estado)
    {
        $stmt = mysqli_prepare($this->conn,
            "UPDATE mantenimiento_camion SET estado = ? WHERE id_mantenimiento = ?");
        mysqli_stmt_bind_param($stmt, "si", $estado, $id);
        if (mysqli_stmt_execute($stmt)) {
            // si se completa, liberar el camion
            if ($estado === 'completado') {
                $row = $this->getById($id);
                if ($row) {
                    mysqli_query($this->conn,
                        "UPDATE camion SET estado='disponible' WHERE id_camion=" . (int)$row['id_camion']);
                }
            }
            return ["success" => "Estado actualizado"];
        }
        return ["error" => "No se pudo actualizar"];
    }
}
