<?php

class Empleado
{
    const ACTIVO = "activo";
    const SUSPENDIDO = "suspendido";
    public $id;
    public $usuario;
    public $clave;
    public $estado;
    public $rol;
    public $fecha_logueo;

    public function crearEmpleado()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO empleados (usuario, clave, estado, rol, fecha_logueo) VALUES (:usuario, :clave, :estado, :rol, :fecha_logueo)");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_logueo', $this->fecha_logueo, PDO::PARAM_STR);
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT empleados.id, empleados.usuario, empleados.clave, empleados.estado, empleados.rol, empleados.fecha_logueo
            FROM empleados"
        );
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }

    public static function obtenerEmpleado($empNombre)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT empleados.id, empleados.usuario, empleados.clave, empleados.estado, empleados.rol, empleados.fecha_logueo
            FROM empleados WHERE usuario = :usuario"
        );
        $consulta->bindValue(':usuario', $empNombre, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchObject('Empleado');
    }

    public static function modificarEmpleado($id, $nuevoEstado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET estado = :estado WHERE id = :id");
        $consulta->bindValue(':estado', $nuevoEstado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarEmpleado($usuarioID)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM empleados WHERE id = :id");
        $consulta->bindValue(':id', $usuarioID, PDO::PARAM_INT);
        $consulta->execute();
    }
}
