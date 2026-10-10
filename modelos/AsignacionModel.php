<?php

class AsignacionModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $sql = "SELECT r.*,
                       u.nombre AS conductor_nombre, u.apellido AS conductor_apellido,
                       c.matricula AS camion, c.estado AS camion_estado,
                       up.nombre AS peon_nombre, up.apellido AS peon_apellido
                FROM ruta r
                LEFT JOIN conductor co ON r.id_conductor = co.id_usuario
                LEFT JOIN usuario u ON u.id_usuario = co.id_usuario
                LEFT JOIN camion c ON r.id_camion = c.id_camion
                LEFT JOIN usuario up ON up.id_usuario = co.id_peon
                ORDER BY r.id_ruta DESC";
        $result = mysqli_query($this->conn, $sql);
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
        return $items;
    }

    public function asignar($id_ruta, $data)
    {
        $stmt = mysqli_prepare($this->conn,
            "UPDATE ruta SET id_conductor = ?, id_camion = ? WHERE id_ruta = ?");
        $id_conductor = !empty($data['id_conductor']) ? $data['id_conductor'] : null;
        $id_camion    = !empty($data['id_camion'])    ? $data['id_camion']    : null;
        mysqli_stmt_bind_param($stmt, "iii", $id_conductor, $id_camion, $id_ruta);
        if (mysqli_stmt_execute($stmt)) {
            // marcar camion en_ruta si se asignó
            if ($id_camion) {
                mysqli_query($this->conn,
                    "UPDATE camion SET estado='en_ruta' WHERE id_camion=" . (int)$id_camion);
            }
            return ["success" => "Asignación actualizada"];
        }
        return ["error" => "No se pudo asignar"];
    }

    public function getConductoresDisponibles()
    {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido, co.zona_asignada, co.turno
                FROM usuario u
                JOIN conductor co ON u.id_usuario = co.id_usuario
                WHERE u.estado = 'activo'
                ORDER BY u.nombre";
        $result = mysqli_query($this->conn, $sql);
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
        return $items;
    }
}
